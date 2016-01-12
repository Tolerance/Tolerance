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
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('header')
                            ->isRequired()
                            ->defaultValue('X-Request-Id')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $builder;
    }
}
