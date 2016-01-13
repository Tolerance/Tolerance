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
                    ->arrayNode('strategy')
                        ->isRequired()
                        ->children();

            foreach ($this->getStrategyNodes() as $node) {
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

    private function getStrategyNodes(array $except = [])
    {
        $builder = new TreeBuilder();
        $nodes = [];

        if (!in_array('max', $except)) {
            $maxNode = $builder->root('max');
            $strategyChildren = $maxNode
                ->children()
                    ->integerNode('count')->isRequired()->end()
                    ->arrayNode('strategy')
                        ->isRequired()
                        ->children();

            foreach ($this->getStrategyNodes($except + ['max']) as $node) {
                $strategyChildren->append($node);
            }

            $nodes[] = $maxNode;
        }

        if (!in_array('exponential', $except)) {
            $exponentialNode = $builder->root('exponential');
            $exponentialNode
                ->children()
                    ->integerNode('exponent')->isRequired()->end()
                    ->scalarNode('waiter')->isRequired()->end()
                ->end()
            ;

            $nodes[] = $exponentialNode;
        }

        return $nodes;
    }
}
