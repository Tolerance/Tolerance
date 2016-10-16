<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Symfony\Bundle\ToleranceBundle\DependencyInjection;

use GuzzleHttp\Client;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Tolerance\Bridge\RabbitMqBundle\MessageProfile\StoreMessageProfileConsumer;
use Tolerance\Bridge\RabbitMqBundle\MessageProfile\StoreMessageProfileProducer;
use Tolerance\MessageProfile\Storage\ElasticaStorage;
use Tolerance\MessageProfile\Storage\Neo4jStorage;
use Tolerance\Metrics\Collector\NamespacedCollector;
use Tolerance\Metrics\Collector\RabbitMq\RabbitMqCollector;
use Tolerance\Metrics\Collector\RabbitMq\RabbitMqHttpClient;
use Tolerance\Metrics\Publisher\HostedGraphitePublisher;
use Tolerance\Metrics\Publisher\LoggerPublisher;
use Tolerance\Operation\Runner\CallbackOperationRunner;
use Tolerance\Operation\Runner\RetryOperationRunner;
use Tolerance\Waiter\ExponentialBackOff;
use Tolerance\Waiter\CountLimited;
use Tolerance\Waiter\NullWaiter;
use Tolerance\Waiter\SleepWaiter;

class ToleranceExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);
        if (!$config['message_profile']['enabled'] || !$config['message_profile']['integrations']['jms_serializer']) {
            return;
        }

        $bundles = $container->getParameter('kernel.bundles');
        if (array_key_exists('JMSSerializerBundle', $bundles)) {
            $container->prependExtensionConfig('jms_serializer', [
                'metadata' => [
                    'directories' => [
                        'ToleranceMessageProfile' => [
                            'namespace_prefix' => 'Tolerance\\MessageProfile\\',
                            'path' => '%kernel.root_dir%/../vendor/tolerance/tolerance/src/Tolerance/Bridge/JMSSerializer/MessageProfile/Resources/config',
                        ],
                    ],
                ],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('tolerance.aop.enabled', $config['aop']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('waiter.xml');

        if ($config['aop']) {
            $this->loadAop($container, $loader);
        }

        if ($config['operation_runner_listener']) {
            $loader->load('operations/listeners.xml');
        }

        if ($config['message_profile']['enabled']) {
            $this->loadMessageProfile($container, $loader, $config['message_profile']);
        }

        foreach ($config['operation_runners'] as $name => $operationRunner) {
            $name = sprintf('tolerance.operation_runners.%s', $name);

            $this->createOperationRunnerDefinition($container, $name, $operationRunner);
        }

        $loader->load('metrics.xml');
        $this->createMetricCollectors($container, $config['metrics']['collectors']);
        $this->createMetricPublishers($container, $config['metrics']['publishers']);
    }

    private function loadMessageProfile(ContainerBuilder $container, LoaderInterface $loader, array $config)
    {
        $container->setParameter('tolerance.message_profile.header', $config['header']);
        $container->setParameter('tolerance.message_profile.current_peer', $config['current_peer']);

        $loader->load('message-profile/listener.xml');
        $loader->load('message-profile/storage.xml');
        $loader->load('message-profile/guzzle.xml');

        $this->configureMessageProfileStorage($container, $loader, $config['storage']);
        $this->loadMessageProfileIntegrations($container, $loader, $config['integrations']);
    }

    private function loadMessageProfileIntegrations(ContainerBuilder $container, LoaderInterface $loader, array $config)
    {
        if ($config['monolog']) {
            $loader->load('message-profile/monolog.xml');
        }

        if ($config['rabbitmq']) {
            $this->decorateRabbitMqConsumersAndProducers($container, $loader);
        }
    }

    private function decorateRabbitMqConsumersAndProducers(ContainerBuilder $container, LoaderInterface $loader)
    {
        foreach ($container->findTaggedServiceIds('old_sound_rabbit_mq.producer') as $id => $attributes) {
            $decoratorId = $id.'.tolerance_decorator';
            $decoratorDefinition = new Definition(StoreMessageProfileProducer::class, [
                new Reference($decoratorId.'.inner'),
                new Reference('tolerance.message_profile.storage'),
                new Reference('tolerance.message_profile.identifier.generator.uuid'),
                new Reference('tolerance.message_profile.peer.resolver.current'),
                new Parameter('tolerance.message_profile.header'),
            ]);

            $decoratorDefinition->setDecoratedService($id);
            $container->setDefinition($decoratorId, $decoratorDefinition);
        }

        foreach ($container->findTaggedServiceIds('old_sound_rabbit_mq.consumer') as $id => $attributes) {
            $decoratorId = $id.'.tolerance_decorator';
            $decoratorDefinition = new Definition(StoreMessageProfileConsumer::class, [
                new Reference($decoratorId.'.inner'),
                new Reference('tolerance.message_profile.storage'),
                new Reference('tolerance.message_profile.identifier.generator.uuid'),
                new Reference('tolerance.message_profile.peer.resolver.current'),
                new Parameter('tolerance.message_profile.header'),
            ]);

            $decoratorDefinition->setDecoratedService($id);
            $container->setDefinition($decoratorId, $decoratorDefinition);
        }
    }

    private function configureMessageProfileStorage(ContainerBuilder $container, LoaderInterface $loader, array $config)
    {
        if (array_key_exists('elastica', $config)) {
            $loader->load('message-profile/jms_serializer.xml');

            $storage = 'tolerance.message_profile.storage.elastica';
            $container->setDefinition($storage, new Definition(
                ElasticaStorage::class,
                [
                    new Reference('tolerance.message_profile.storage.normalizer.jms_serializer'),
                    new Reference($config['elastica']),
                ]
            ));
        } elseif (array_key_exists('neo4j', $config)) {
            $storage = 'tolerance.message_profile.storage.neo4j';
            $container->setDefinition($storage, new Definition(
                Neo4jStorage::class,
                [
                    new Reference($config['neo4j']),
                    new Reference('tolerance.message_profile.storage.profile_normalizer.simple'),
                ]
            ));
        } elseif (false !== $config['in_memory']) {
            $storage = 'tolerance.message_profile.storage.in_memory';
        } else {
            throw new \RuntimeException('Unable to configure Request Identifier storage');
        }

        $container->setAlias('tolerance.message_profile.storage', $storage);

        if ($config['buffered']) {
            $loader->load('message-profile/storage/buffered.xml');
        }
    }

    private function loadAop(ContainerBuilder $container, LoaderInterface $loader)
    {
        $bundles = $container->getParameter('kernel.bundles');
        if (!array_key_exists('JMSAopBundle', $bundles)) {
            throw new \RuntimeException('You need to add the JMSAopBundle if you want to use the AOP feature');
        }

        $loader->load('operations/aop.xml');
    }

    private function createOperationRunnerDefinition(ContainerBuilder $container, $name, array $config)
    {
        if (array_key_exists('retry', $config)) {
            return $this->createRetryOperationRunnerDefinition($container, $name, $config['retry']);
        } elseif (array_key_exists('callback', $config)) {
            return $this->createCallbackOperationRunnerDefinition($container, $name);
        }

        throw new \RuntimeException(sprintf(
            'No valid operation runner found in %s',
            implode(', ', array_keys($config))
        ));
    }

    private function createRetryOperationRunnerDefinition(ContainerBuilder $container, $name, array $config)
    {
        $decoratedRunnerName = $this->createOperationRunnerDefinition($container, $name.'.runner', $config['runner']);
        $waiterName = $this->createWaiterDefinition($container, $name.'.waiter', $config['waiter']);

        $container->setDefinition($name,  $this->createDefinition(RetryOperationRunner::class, [
            new Reference($decoratedRunnerName),
            new Reference($waiterName),
        ]));

        return $name;
    }

    private function createCallbackOperationRunnerDefinition(ContainerBuilder $container, $name)
    {
        $container->setDefinition($name, $this->createDefinition(CallbackOperationRunner::class));

        return $name;
    }

    private function createWaiterDefinition(ContainerBuilder $container, $name, array $config)
    {
        if (array_key_exists('count_limited', $config)) {
            return $this->createCouldLimitedWaiterDefinition($container, $name, $config['count_limited']);
        } elseif (array_key_exists('exponential_back_off', $config)) {
            return $this->createExponentialBackOffWaiterDefinition($container, $name, $config['exponential_back_off']);
        } elseif (array_key_exists('sleep', $config)) {
            return $this->createSleepWaiterDefinition($container, $name);
        } elseif (array_key_exists('null', $config)) {
            return $this->createNullWaiterDefinition($container, $name);
        }

        throw new \RuntimeException(sprintf(
            'No valid wait strategy found in %s',
            implode(', ', array_keys($config))
        ));
    }

    private function createCouldLimitedWaiterDefinition(ContainerBuilder $container, $name, array $config)
    {
        $decoratedStrategyName = $this->createWaiterDefinition($container, $name.'.waiter', $config['waiter']);

        $container->setDefinition($name, new Definition(CountLimited::class, [
            new Reference($decoratedStrategyName),
            $config['count'],
        ]));

        return $name;
    }

    private function createExponentialBackOffWaiterDefinition(ContainerBuilder $container, $name, array $config)
    {
        $decoratedWaiterName = $this->createWaiterDefinition($container, $name.'.waiter', $config['waiter']);

        $container->setDefinition($name, new Definition(ExponentialBackOff::class, [
            new Reference($decoratedWaiterName),
            $config['exponent'],
        ]));

        return $name;
    }

    private function createSleepWaiterDefinition(ContainerBuilder $container, $name)
    {
        $container->setDefinition($name, new Definition(SleepWaiter::class));

        return $name;
    }

    private function createNullWaiterDefinition(ContainerBuilder $container, $name)
    {
        $container->setDefinition($name, new Definition(NullWaiter::class));

        return $name;
    }

    private function createDefinition($className, array $arguments = [])
    {
        $definition = new Definition($className, $arguments);
        $definition->addTag('tolerance.operation_runner');

        return $definition;
    }

    private function createMetricCollectors(ContainerBuilder $container, array $collectors)
    {
        foreach ($collectors as $name => $collector) {
            $definition = $this->createMetricCollector($container, $name, $collector)->addTag('tolerance.metrics.collector');
        }
    }

    private function createMetricCollector(ContainerBuilder $container, $name, array $collector)
    {
        $serviceName = 'tolerance.metrics.collector.'.$name;
        if ($collector['type'] == 'rabbitmq') {
            $options = $collector['options'];

            $httpClientServiceName = $serviceName.'.http_client';
            $container->setDefinition($httpClientServiceName, new Definition(Client::class));

            $clientServiceName = $serviceName.'.client';
            $container->setDefinition($clientServiceName, new Definition(RabbitMqHttpClient::class, [
                new Reference($httpClientServiceName),
                $options['host'],
                $options['port'],
                $options['username'],
                $options['password'],
            ]));

            $definition = $container->setDefinition($serviceName, new Definition(RabbitMqCollector::class, [
                new Reference($clientServiceName),
                $options['vhost'],
                $options['queue'],
            ]));
        } else {
            throw new \InvalidArgumentException(sprintf('Type "%s" not supported', $collector['type']));
        }

        if ($collector['namespace']) {
            $definition = $container->setDefinition($serviceName.'.namespaced', new Definition(NamespacedCollector::class, [
                new Reference($serviceName),
                $collector['namespace'],
            ]));
        }

        return $definition;
    }

    private function createMetricPublishers(ContainerBuilder $container, array $publishers)
    {
        foreach ($publishers as $name => $publisher) {
            $this->createMetricPublisher($container, $name, $publisher)->addTag('tolerance.metrics.publisher');
        }
    }

    private function createMetricPublisher(ContainerBuilder $container, $name, array $publisher)
    {
        $serviceName = 'tolerance.metrics.publisher.'.$name;

        if ('logger' == $publisher['type']) {
            return $container->setDefinition($serviceName, new Definition(LoggerPublisher::class, [
                new Reference('logger'),
            ]));
        }

        if ('hosted_graphite' == $publisher['type']) {
            return $container->setDefinition($serviceName, new Definition(HostedGraphitePublisher::class, [
                $publisher['options']['server'],
                $publisher['options']['port'],
                $publisher['options']['api_key'],
            ]));
        }

        throw new \RuntimeException(sprintf(
            'Publisher "%s" not supported',
            $publisher['type']
        ));
    }
}
