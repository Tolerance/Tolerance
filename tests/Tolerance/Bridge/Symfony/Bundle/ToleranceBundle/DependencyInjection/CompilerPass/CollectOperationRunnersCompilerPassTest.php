<?php

namespace Tolerance\Bridge\Symfony\Bundle\ToleranceBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Reference;

class CollectOperationRunnersCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_adds_the_operation_runners_to_the_registry()
    {
        $compilerPass = new CollectOperationRunnersCompilerPass();

        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface', $compilerPass);

        $containerBuilder = $this->prophesize('Symfony\Component\DependencyInjection\ContainerBuilder');
        $containerBuilder->findTaggedServiceIds('tolerance.operation_runner')->willReturn([
            'my.service' => [[]],
        ]);

        $runnerRepositoryDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $containerBuilder->getDefinition('tolerance.operation_runner_registry')->willReturn($runnerRepositoryDefinition);
        $runnerRepositoryDefinition->addMethodCall('registerOperationRunner', [new Reference('my.service')])->shouldBeCalled();

        $compilerPass->process($containerBuilder->reveal());
    }
}
