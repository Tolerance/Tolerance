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
                ->arrayNode('request_identifier')
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
                ->end()
                ->booleanNode('aop')->defaultFalse()->end()
            ->end()
        ;

        return $builder;
    }
}
