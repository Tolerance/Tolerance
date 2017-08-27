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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();

        $root = $builder->root('tolerance');

        $root
            ->children()
            ->append($this->getOperationRunnersNode())
            ->append($this->getMetricsNode())
            ->booleanNode('operation_runner_listener')->defaultTrue()->end()
            ->booleanNode('guzzle')->defaultFalse()->end()
            ->append($this->getAopNode())
            ->append($this->getTracerNode())
            ->end();

        return $builder;
    }

    private function getOperationRunnersNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('operation_runners');

        $children = $node
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->children();

        foreach ($this->getOperationRunnerNodes() as $runnerNode) {
            $children->append($runnerNode);
        }

        $children
            ->end()
            ->end();

        return $node;
    }

    private function getOperationRunnerNodes(array $except = [])
    {
        $builder = new TreeBuilder();
        $nodes = [];

        if (!in_array('retry', $except)) {
            $retryNode = $builder->root('retry');
            $children = $retryNode
                ->children()
                    ->arrayNode('waiter')
                    ->isRequired()
                        ->children();

            foreach ($this->getWaiterNodes() as $node) {
                $children->append($node);
            }

            $runnerChildren = $retryNode
                ->children()
                    ->arrayNode('runner')
                        ->children();

            foreach ($this->getOperationRunnerNodes($except + ['retry']) as $runner) {
                $runnerChildren->append($runner);
            }

            $nodes[] = $retryNode;
        }

        if (!in_array('callback', $except)) {
            $retryNode = $builder->root('callback');

            $nodes[] = $retryNode;
        }

        if (!in_array('success_failure_metrics', $except)) {
            $successFailureMetricsNode = $builder->root('success_failure_metrics');

            $successFailureMetricsNode
                ->children()
                    ->scalarNode('runner')->defaultValue('tolerance.operation_runners.default')->end()
                    ->scalarNode('publisher')->isRequired()->end()
                    ->scalarNode('namespace')->isRequired()->end()
                ->end()
            ;

            $nodes[] = $successFailureMetricsNode;
        }

        if (!in_array('buffered', $except)) {
            $bufferedNode = $builder->root('buffered');

            $bufferedNode
                ->children()
                    ->scalarNode('runner')->defaultValue('tolerance.operation_runners.default')->end()
                    ->scalarNode('buffer')->defaultValue('in_memory')->end()
                ->end()
            ;

            $nodes[] = $bufferedNode;
        }

        if (!in_array('placeholder', $except)) {
            $placeholderNode = $builder->root('placeholder');

            $placeholderNode
                ->children()
                    ->scalarNode('runner')->defaultValue('tolerance.operation_runners.default')->end()
                    ->variableNode('value')->defaultNull()->end()
                    ->scalarNode('logger')->defaultNull()->end()
                ->end()
            ;

            $nodes[] = $placeholderNode;
        }

        return $nodes;
    }

    private function getWaiterNodes(array $except = [])
    {
        $builder = new TreeBuilder();
        $nodes = [];

        if (!in_array('count_limited', $except)) {
            $maxNode = $builder->root('count_limited');
            $strategyChildren = $maxNode
                ->children()
                ->integerNode('count')->isRequired()->end()
                ->arrayNode('waiter')
                ->isRequired()
                ->children();

            array_push($except, 'count_limited');
            foreach ($this->getWaiterNodes($except) as $node) {
                $strategyChildren->append($node);
            }

            $nodes[] = $maxNode;
        }

        if (!in_array('exponential_back_off', $except)) {
            $exponentialNode = $builder->root('exponential_back_off');
            $strategyChildren = $exponentialNode
                ->children()
                ->floatNode('exponent')->end()
                ->floatNode('initial_exponent')->end()
                ->floatNode('step')->defaultValue(1.0)->end()
                ->arrayNode('waiter')
                ->isRequired()
                ->children();

            array_push($except, 'exponential_back_off');
            foreach ($this->getWaiterNodes($except) as $node) {
                $strategyChildren->append($node);
            }

            $nodes[] = $exponentialNode;
        }

        if (!in_array('timeout', $except)) {
            $timeoutNode = $builder->root('timeout');
            $strategyChildren = $timeoutNode
                ->children()
                ->integerNode('timeout')->isRequired()->end()
                ->arrayNode('waiter')->isRequired()->children();

            array_push($except, 'timeout');
            foreach ($this->getWaiterNodes($except) as $node) {
                $strategyChildren->append($node);
            }

            $nodes[] = $timeoutNode;
        }

        if (!in_array('null', $except)) {
            $nodes[] = $builder->root('null');
        }

        if (!in_array('sleep', $except)) {
            $nodes[] = $builder->root('sleep');
        }

        return $nodes;
    }

    private function getMetricsNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('metrics');

        $node
            ->children()
                ->arrayNode('collectors')
                    ->defaultValue([])
                    ->prototype('array')
                        ->children()
                            ->scalarNode('type')->isRequired()->end()
                            ->scalarNode('namespace')->isRequired()->end()
                            ->variableNode('options')->defaultValue([])->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('publishers')
                    ->defaultValue([])
                    ->prototype('array')
                        ->children()
                            ->scalarNode('type')->isRequired()->end()
                            ->scalarNode('operation_runner')->end()
                            ->variableNode('options')->defaultValue([])->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('command')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('collector')->defaultValue('tolerance.metrics.collector.collection')->end()
                        ->scalarNode('publisher')->defaultValue('tolerance.metrics.publisher.collection')->end()
                    ->end()
                ->end()
                ->arrayNode('request')
                    ->children()
                        ->scalarNode('publisher')->defaultValue('tolerance.metrics.publisher.collection')->end()
                        ->scalarNode('namespace')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    private function getAopNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('aop');

        $node
            ->canBeEnabled()
            ->children()
                ->arrayNode('wrappers')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('class')->isRequired()->end()
                            ->arrayNode('methods')
                                ->isRequired()
                                ->prototype('scalar')
                                ->end()
                            ->end()
                            ->scalarNode('runner')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    private function getTracerNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('tracer');

        $node
            ->canBeEnabled()
            ->children()
                ->scalarNode('service_name')->defaultNull()->end()
                ->scalarNode('operation_runner')->defaultNull()->end()
                ->arrayNode('zipkin')
                    ->isRequired() // As it's the only backend at the moment
                    ->children()
                        ->arrayNode('http')
                            ->isRequired() // As it's the only transport at the moment
                            ->children()
                                ->scalarNode('base_url')->isRequired()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('rabbitmq')
                    ->canBeEnabled()
                    ->children()
                        ->arrayNode('consumers')
                            ->defaultValue([])
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
