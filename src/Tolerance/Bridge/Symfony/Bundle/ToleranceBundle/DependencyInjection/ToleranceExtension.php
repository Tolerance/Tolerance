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
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Tolerance\Operation\Runner\CallbackOperationRunner;
use Tolerance\Operation\Runner\RetryOperationRunner;
use Tolerance\Waiter\Strategy\Exponential;
use Tolerance\Waiter\Strategy\Max;

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

        if ($config['request_identifier']['enabled']) {
            $this->loadRequestIdentifier($container, $loader, $config['request_identifier']);
        }

        if ($config['aop']) {
            $this->loadAop($container, $loader);
        }

        foreach ($config['operation_runners'] as $name => $operationRunner) {
            $name = sprintf('tolerance.operation_runners.%s', $name);

            $this->createOperationRunnerDefinition($container, $name, $operationRunner);
        }
    }

    private function loadRequestIdentifier(ContainerBuilder $container, LoaderInterface $loader, array $config)
    {
        $container->setParameter('tolerance.request_identifier.header', $config['header']);

        $loader->load('request-identifier/request.xml');
        $loader->load('request-identifier/listener.xml');

        if ($config['monolog']) {
            $loader->load('request-identifier/monolog.xml');
        }
    }

    private function loadAop(ContainerBuilder $container, LoaderInterface $loader)
    {
        $bundles = $container->getParameter('kernel.bundles');
        if (!array_key_exists('JMSAopBundle', $bundles)) {
            throw new \RuntimeException('You need to add the JMSAopBundle is you want to use the AOP feature');
        }

        $loader->load('aop.xml');
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
        $waitStrategyName = $this->createWaitStrategyDefinition($container, $name.'.wait_strategy', $config['strategy']);

        $container->setDefinition($name,  new Definition(RetryOperationRunner::class, [
            new Reference($decoratedRunnerName),
            new Reference($waitStrategyName),
        ]));

        return $name;
    }

    private function createCallbackOperationRunnerDefinition(ContainerBuilder $container, $name)
    {
        $container->setDefinition($name, new Definition(CallbackOperationRunner::class));

        return $name;
    }

    private function createWaitStrategyDefinition(ContainerBuilder $container, $name, array $config)
    {
        if (array_key_exists('max', $config)) {
            return $this->createMaxWaitStrategyDefinition($container, $name, $config['max']);
        } elseif (array_key_exists('exponential', $config)) {
            return $this->createExponentialWaitStrategyDefinition($container, $name, $config['exponential']);
        }

        throw new \RuntimeException(sprintf(
            'No valid wait strategy found in %s',
            implode(', ', array_keys($config))
        ));
    }

    private function createMaxWaitStrategyDefinition(ContainerBuilder $container, $name, array $config)
    {
        $decoratedStrategyName = $this->createWaitStrategyDefinition($container, $name.'.strategy', $config['strategy']);

        $container->setDefinition($name, new Definition(Max::class, [
            new Reference($decoratedStrategyName),
            $config['count'],
        ]));

        return $name;
    }

    private function createExponentialWaitStrategyDefinition(ContainerBuilder $container, $name, array $config)
    {
        $container->setDefinition($name, new Definition(Exponential::class, [
            new Reference($config['waiter']),
            $config['exponent'],
        ]));

        return $name;
    }
}
