<?php

namespace Tolerance\Bridge\Symfony\Bundle\ToleranceBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CollectMetricsCollectorsAndPublishers implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('tolerance.metrics.collector.collection')) {
            return;
        }

        $collectorCollection = $container->getDefinition('tolerance.metrics.collector.collection');
        foreach ($container->findTaggedServiceIds('tolerance.metrics.collector') as $serviceId => $tags) {
            $collectorCollection->addMethodCall('addCollector', [new Reference($serviceId)]);
        }

        $publisherCollection = $container->getDefinition('tolerance.metrics.publisher.collection');
        foreach ($container->findTaggedServiceIds('tolerance.metrics.publisher') as $serviceId => $tags) {
            $publisherCollection->addMethodCall('addPublisher', [new Reference($serviceId)]);
        }
    }
}
