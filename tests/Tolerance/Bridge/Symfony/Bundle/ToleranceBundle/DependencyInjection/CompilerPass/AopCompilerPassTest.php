<?php

namespace Tolerance\Bridge\Symfony\Bundle\ToleranceBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Reference;

class AopCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_adds_the_runner_call()
    {
        $compilerPass = new AopCompilerPass();

        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface', $compilerPass);

        $containerBuilder = $this->prophesize('Symfony\Component\DependencyInjection\ContainerBuilder');
        $containerBuilder->findTaggedServiceIds('tolerance.operation_wrapper')->willReturn([
            'my.service' => [
                ['methods' => 'foo', 'runner' => 'tolerance.my_runner']
            ],
        ]);

        $serviceDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $serviceDefinition->getClass()->willReturn('My\\Service\\Class');
        $containerBuilder->getDefinition('my.service')->willReturn($serviceDefinition);

        $runnerRepositoryDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $containerBuilder->getDefinition('tolerance.aop.runner_repository')->willReturn($runnerRepositoryDefinition);

        $runnerRepositoryDefinition->addMethodCall('addRunnerAt', ['My\\Service\\Class:foo', new Reference('tolerance.my_runner')])->shouldBeCalled();

        $compilerPass->process($containerBuilder->reveal());
    }
}
