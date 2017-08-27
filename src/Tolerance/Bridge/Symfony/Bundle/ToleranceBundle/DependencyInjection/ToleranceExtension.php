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
use GuzzleHttp\ClientInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Tolerance\Bridge\Symfony\Metrics\EventListener\RequestEnded\SendRequestTimeToPublisher;
use Tolerance\Bridge\Symfony\Metrics\Request\StaticRequestMetricNamespaceResolver;
use Tolerance\Metrics\Collector\NamespacedCollector;
use Tolerance\Metrics\Collector\RabbitMq\RabbitMqCollector;
use Tolerance\Metrics\Collector\RabbitMq\RabbitMqHttpClient;
use Tolerance\Metrics\Publisher\BeberleiMetricsAdapterPublisher;
use Tolerance\Metrics\Publisher\DelegatesToOperationRunnerPublisher;
use Tolerance\Metrics\Publisher\HostedGraphitePublisher;
use Tolerance\Metrics\Publisher\LoggerPublisher;
use Tolerance\Operation\Buffer\InMemoryOperationBuffer;
use Tolerance\Operation\Placeholder\PlaceholderResponseResolver;
use Tolerance\Operation\Placeholder\ValueConstructedPlaceholderResponseResolver;
use Tolerance\Operation\Runner\BufferedOperationRunner;
use Tolerance\Operation\Runner\CallbackOperationRunner;
use Tolerance\Operation\Runner\Metrics\SuccessFailurePublisherOperationRunner;
use Tolerance\Operation\Runner\PlaceholderOperationRunner;
use Tolerance\Operation\Runner\RetryOperationRunner;
use Tolerance\Waiter\ExponentialBackOff;
use Tolerance\Waiter\CountLimited;
use Tolerance\Waiter\NullWaiter;
use Tolerance\Waiter\SleepWaiter;
use Tolerance\Waiter\TimeOut;

class ToleranceExtension extends Extension
{
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

        if ($config['aop']['enabled']) {
            $loader->load('operations/aop.xml');

            $this->loadAop($container, $config['aop']);
        }

        if ($config['operation_runner_listener']) {
            $loader->load('operations/listeners.xml');
        }

        if ($config['guzzle']) {
            $loader->load('operations/guzzle.xml');
        }

        if ($config['tracer']['enabled']) {
            $loader->load('tracer.xml');
            $container->setParameter('tolerance.tracer.service_name', $config['tracer']['service_name']);

            if (array_key_exists('zipkin', $config['tracer'])) {
                if (array_key_exists('http', $config['tracer']['zipkin'])) {
                    $container->setParameter('tolerance.tracer.zipkin.http.base_url', $config['tracer']['zipkin']['http']['base_url']);
                    $tracer = 'tolerance.tracer.zipkin.http';
                }
            }

            if (!isset($tracer)) {
                throw new \InvalidArgumentException('No tracer configured');
            }

            if (null !== ($runner = $config['tracer']['operation_runner'])) {
                $container->getDefinition($tracer)->addTag('tolerance.operation_wrapper', [
                    'runner' => $runner,
                    'methods' => 'trace',
                ]);
            }

            $container->setAlias('tolerance.tracer', $tracer);

            if ($container->getParameter('kernel.debug')) {
                $loader->load('tracer/debug.xml');
            }

            if (interface_exists('GuzzleHttp\ClientInterface')) {
                if (version_compare(ClientInterface::VERSION, '6.0') >= 0) {
                    $loader->load('tracer/guzzle/6.x.xml');
                } else {
                    $loader->load('tracer/guzzle/4.x-5.x.xml');
                }
            }

            if ($config['tracer']['rabbitmq']['enabled']) {
                $container->setParameter('tolerance.tracer.rabbitmq.enabled', true);
                $container->setParameter('tolerance.tracer.rabbitmq.consumers', $config['tracer']['rabbitmq']['consumers']);
            }
        }

        foreach ($config['operation_runners'] as $name => $operationRunner) {
            $name = sprintf('tolerance.operation_runners.%s', $name);

            $this->createOperationRunnerDefinition($container, $name, $operationRunner);
        }

        if (array_key_exists('metrics', $config)) {
            $loader->load('metrics.xml');

            $this->createMetricCollectors($container, $config['metrics']['collectors']);
            $this->createMetricPublishers($container, $config['metrics']['publishers']);

            // Configure the metrics command
            $container
                ->getDefinition('tolerance.metrics.command.collect_and_publish')
                ->replaceArgument(0, new Reference($config['metrics']['command']['collector']))
                ->replaceArgument(1, new Reference($config['metrics']['command']['publisher']))
            ;

            // Configure the request listeners
            if (array_key_exists('request', $config['metrics'])) {
                $this->configureRequestListeners($container, $config['metrics']);
            }
        }
    }

    private function loadAop(ContainerBuilder $container, array $config)
    {
        $bundles = $container->getParameter('kernel.bundles');
        if (!array_key_exists('JMSAopBundle', $bundles)) {
            throw new \RuntimeException('You need to add the JMSAopBundle if you want to use the AOP feature');
        }

        foreach ($config['wrappers'] as $wrapper) {
            $this->createAopWrapper($container, $wrapper);
        }
    }

    private function createOperationRunnerDefinition(ContainerBuilder $container, $name, array $config)
    {
        if (array_key_exists('retry', $config)) {
            return $this->createRetryOperationRunnerDefinition($container, $name, $config['retry']);
        } elseif (array_key_exists('callback', $config)) {
            return $this->createCallbackOperationRunnerDefinition($container, $name);
        } elseif (array_key_exists('success_failure_metrics', $config)) {
            return $this->createSuccessFailureMetricsOperationRunnerDefinition($container, $name, $config['success_failure_metrics']);
        } elseif (array_key_exists('buffered', $config)) {
            return $this->createBufferedOperationRunnerDefinition($container, $name, $config['buffered']);
        } elseif (array_key_exists('placeholder', $config)) {
            return $this->createPlaceholderOperationRunnerDefinition($container, $name, $config['placeholder']);
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

        $container->setDefinition($name, $this->createDefinition(RetryOperationRunner::class, [
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

    private function createSuccessFailureMetricsOperationRunnerDefinition(ContainerBuilder $container, $name, array $config)
    {
        $definition = $this->createDefinition(SuccessFailurePublisherOperationRunner::class, [
            new Reference($config['runner']),
            new Reference($config['publisher']),
            $config['namespace'],
        ]);

        $container->setDefinition($name, $definition);

        return $name;
    }

    private function createBufferedOperationRunnerDefinition(ContainerBuilder $container, $name, array $config)
    {
        if ($config['buffer'] == 'in_memory') {
            $buffer = new Definition(InMemoryOperationBuffer::class);
        } else {
            $buffer = new Reference($config['buffer']);
        }

        $definition = $this->createDefinition(BufferedOperationRunner::class, [
            new Reference($config['runner']),
            $buffer,
        ]);

        $container->setDefinition($name, $definition);

        return $name;
    }

    private function createPlaceholderOperationRunnerDefinition(ContainerBuilder $container, $name, array $config)
    {
        $definition = $this->createDefinition(PlaceholderOperationRunner::class, [
            new Reference($config['runner']),
            new Definition(ValueConstructedPlaceholderResponseResolver::class, [$config['value']]),
            null,
            $config['logger'] !== null ? new Reference($config['logger']) : null,
        ]);

        $container->setDefinition($name, $definition);

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
        } elseif (array_key_exists('timeout', $config)) {
            return $this->createTimeoutWaiterDefinition($container, $name, $config['timeout']);
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

        // BC
        if (isset($config['exponent'])) {
            if (isset($config['initial_exponent'])) {
                throw new \RuntimeException('The `initial_exponent` has replaced the `exponent` configuration, please only use `initial_exponent`.');
            }

            $config['initial_exponent'] = $config['exponent'];
        }

        $container->setDefinition($name, new Definition(ExponentialBackOff::class, [
            new Reference($decoratedWaiterName),
            $config['initial_exponent'],
            $config['step']
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

    private function createTimeoutWaiterDefinition(ContainerBuilder $container, $name, array $config)
    {
        $decoratedWaiterName = $this->createWaiterDefinition($container, $name.'.waiter', $config['waiter']);

        $container->setDefinition($name, new Definition(TimeOut::class, [
            new Reference($decoratedWaiterName),
            $config['timeout']
        ]));

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
            $this->createMetricCollector($container, $name, $collector)->addTag('tolerance.metrics.collector');
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
            $container->setDefinition($serviceName.'.inner', $container->getDefinition($serviceName));
            $definition = $container->setDefinition($serviceName, new Definition(NamespacedCollector::class, [
                new Reference($serviceName.'.inner'),
                $collector['namespace'],
            ]));
        }

        return $definition;
    }

    private function createMetricPublishers(ContainerBuilder $container, array $publishers)
    {
        foreach ($publishers as $name => $publisher) {
            $this->createMetricPublisher($container, $name, $publisher);
        }
    }

    private function createMetricPublisher(ContainerBuilder $container, $name, array $publisher)
    {
        $serviceName = 'tolerance.metrics.publisher.'.$name;

        if ('logger' == $publisher['type']) {
            $definiton = $container->setDefinition($serviceName, new Definition(LoggerPublisher::class, [
                new Reference('logger'),
            ]));
        } elseif ('hosted_graphite' == $publisher['type']) {
            $definiton = $container->setDefinition($serviceName, new Definition(HostedGraphitePublisher::class, [
                $publisher['options']['server'],
                $publisher['options']['port'],
                $publisher['options']['api_key'],
            ]));
        } elseif ('beberlei' == $publisher['type']) {
            $definiton = $container->setDefinition($serviceName, new Definition(BeberleiMetricsAdapterPublisher::class, [
                new Reference($publisher['options']['service']),
                array_key_exists('auto_flush', $publisher['options']) ? (bool) $publisher['options']['auto_flush'] : true,
            ]));
        } else {
            throw new \RuntimeException(sprintf(
                'Publisher "%s" not supported',
                $publisher['type']
            ));
        }

        if (isset($publisher['operation_runner'])) {
            $definiton = $container->setDefinition($serviceName, new Definition(DelegatesToOperationRunnerPublisher::class, [
                $container->getDefinition($serviceName),
                new Reference($publisher['operation_runner'])
            ]));
        }

        $definiton->addTag('tolerance.metrics.publisher');
    }

    private function createAopWrapper(ContainerBuilder $container, array $wrapper)
    {
        $runnerRepositoryDefinition = $container->getDefinition('tolerance.aop.runner_repository');

        foreach ($wrapper['methods'] as $method) {
            $runnerRepositoryDefinition->addMethodCall('addRunnerAt', [
                sprintf('%s:%s', $wrapper['class'], $method),
                new Reference($wrapper['runner']),
            ]);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param $config
     */
    private function configureRequestListeners(ContainerBuilder $container, $config)
    {
        $listenerName = 'tolerance.metrics.listener.request_ended.send_time_to_publishers';
        $requestMetricNamespaceResolverName = $listenerName.'.request_metric_namespace_resolver';
        $container->setDefinition($requestMetricNamespaceResolverName, new Definition(StaticRequestMetricNamespaceResolver::class, [
            $config['request']['namespace'],
        ]));

        $container->setDefinition($listenerName,
            (
            new Definition(SendRequestTimeToPublisher::class, [
                new Reference($config['request']['publisher']),
                new Reference($requestMetricNamespaceResolverName),
                new Reference('logger', ContainerInterface::IGNORE_ON_INVALID_REFERENCE),
            ])
            )->addTag('kernel.event_subscriber')
        );
    }
}
