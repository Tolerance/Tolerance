<?php

namespace Tolerance\Bridge\Symfony\Bundle\ToleranceBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CollectOperationRunnersCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $registryDefinition = $container->getDefinition('tolerance.operation_runner_registry');

        foreach ($container->findTaggedServiceIds('tolerance.operation_runner') as $id => $tags) {
            $registryDefinition->addMethodCall('registerOperationRunner', [
                new Reference($id),
            ]);
        }
    }
}
