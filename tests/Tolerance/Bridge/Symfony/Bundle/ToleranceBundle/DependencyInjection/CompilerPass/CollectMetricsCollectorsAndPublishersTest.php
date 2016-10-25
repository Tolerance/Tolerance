<?php

namespace Tolerance\Bridge\Symfony\Bundle\ToleranceBundle\DependencyInjection\CompilerPass;

use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class CollectMetricsCollectorsAndPublishersTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_is_a_compiler_pass()
    {
        $this->assertInstanceOf(
            'Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface',
            new CollectMetricsCollectorsAndPublishers()
        );
    }

    public function test_it_adds_the_metrics_collectors()
    {
        $containerBuilder = $this->getContainerBuilder();
        $containerBuilder->findTaggedServiceIds('tolerance.metrics.collector')->willReturn([
            'my.service' => [[]],
        ]);

        $runnerRepositoryDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $containerBuilder->getDefinition('tolerance.metrics.collector.collection')->willReturn($runnerRepositoryDefinition);
        $runnerRepositoryDefinition->addMethodCall('addCollector', [new Reference('my.service')])->shouldBeCalled();

        $compilerPass = new CollectMetricsCollectorsAndPublishers();
        $compilerPass->process($containerBuilder->reveal());
    }

    public function test_it_adds_the_metrics_publishers()
    {
        $containerBuilder = $this->getContainerBuilder();
        $containerBuilder->findTaggedServiceIds('tolerance.metrics.publisher')->willReturn([
            'my.service' => [[]],
        ]);

        $runnerRepositoryDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $containerBuilder->getDefinition('tolerance.metrics.publisher.collection')->willReturn($runnerRepositoryDefinition);
        $runnerRepositoryDefinition->addMethodCall('addPublisher', [new Reference('my.service')])->shouldBeCalled();

        $compilerPass = new CollectMetricsCollectorsAndPublishers();
        $compilerPass->process($containerBuilder->reveal());
    }

    public function test_it_do_not_collect_anything_if_collection_collector_is_not_found()
    {
        $containerBuilder = $this->getContainerBuilder();
        $containerBuilder->hasDefinition('tolerance.metrics.collector.collection')->willReturn(false);
        $containerBuilder->findTaggedServiceIds('tolerance.metrics.collector')->shouldNotBeCalled();

        $compilerPass = new CollectMetricsCollectorsAndPublishers();
        $compilerPass->process($containerBuilder->reveal());

    }

    private function getContainerBuilder()
    {
        $containerBuilder = $this->prophesize('Symfony\Component\DependencyInjection\ContainerBuilder');
        $containerBuilder->findTaggedServiceIds(Argument::type('string'))->willReturn([]);
        $containerBuilder->getDefinition(Argument::type('string'))->willReturn(
            $this->prophesize(Definition::class)
        );

        $containerBuilder->hasDefinition('tolerance.metrics.collector.collection')->willReturn(true);

        return $containerBuilder;
    }
}
