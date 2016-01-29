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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Tolerance\MessageProfile\Storage\ElasticaStorage;
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
        $bundles = $container->getParameter('kernel.bundles');
        if (array_key_exists('JMSSerializerBundle', $bundles)) {
            $container->prependExtensionConfig('jms_serializer', [
                'metadata' => [
                    'directories' => [
                        'ToleranceMessageProfile' => [
                            'namespace_prefix' => 'Tolerance\\MessageProfile\\',
                            'path' => '%kernel.root_dir%/../vendor/sroze/tolerance/src/Tolerance/Bridge/JMSSerializer/MessageProfile/Resources/config',
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
    }

    private function loadMessageProfile(ContainerBuilder $container, LoaderInterface $loader, array $config)
    {
        $container->setParameter('tolerance.message_profile.header', $config['header']);

        $loader->load('message-profile/listener.xml');
        $loader->load('message-profile/storage.xml');
        $loader->load('message-profile/guzzle.xml');

        if ($config['monolog']) {
            $loader->load('message-profile/monolog.xml');
        }

        $this->configureMessageProfileStorage($container, $loader, $config['storage']);
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
        } else if (false !== $config['in_memory']) {
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
            throw new \RuntimeException('You need to add the JMSAopBundle is you want to use the AOP feature');
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
}
