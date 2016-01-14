<?php

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
                ->append($this->getRequestIdentifierNode())
                ->append($this->getOperationRunnersNode())
                ->booleanNode('aop')->defaultFalse()->end()
            ->end()
        ;

        return $builder;
    }

    private function getRequestIdentifierNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('request_identifier');

        $node
            ->addDefaultsIfNotSet()
            ->canBeEnabled()
            ->children()
                ->scalarNode('header')
                    ->cannotBeEmpty()
                    ->defaultValue('X-Request-Id')
                ->end()
                ->booleanNode('monolog')
                    ->defaultTrue()
                ->end()
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
            ->end()
        ;

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
}
