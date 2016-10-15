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
            ->append($this->getMessageProfileNode())
            ->append($this->getOperationRunnersNode())
            ->append($this->getMetricsNode())
            ->booleanNode('operation_runner_listener')->defaultTrue()->end()
            ->booleanNode('aop')->defaultFalse()->end()
            ->end();

        return $builder;
    }

    private function getMessageProfileNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('message_profile');

        $node
            ->addDefaultsIfNotSet()
            ->canBeEnabled()
            ->children()
                ->scalarNode('header')->cannotBeEmpty()->defaultValue('x-message-id')->end()
                ->arrayNode('integrations')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('monolog')->defaultTrue()->end()
                        ->booleanNode('rabbitmq')->defaultTrue()->end()
                        ->booleanNode('jms_serializer')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('storage')
                    ->isRequired()
                    ->children()
                        ->booleanNode('in_memory')->defaultFalse()->end()
                        ->booleanNode('buffered')->defaultTrue()->end()
                        ->scalarNode('elastica')->cannotBeEmpty()->end()
                        ->scalarNode('neo4j')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->variableNode('current_peer')->defaultValue([])->end()
            ->end()
        ;

        return $node;
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
                ->integerNode('exponent')->isRequired()->end()
                ->arrayNode('waiter')
                ->isRequired()
                ->children();

            array_push($except, 'exponential_back_off');
            foreach ($this->getWaiterNodes($except) as $node) {
                $strategyChildren->append($node);
            }

            $nodes[] = $exponentialNode;
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
            ->addDefaultsIfNotSet()
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
                            ->variableNode('options')->defaultValue([])->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
