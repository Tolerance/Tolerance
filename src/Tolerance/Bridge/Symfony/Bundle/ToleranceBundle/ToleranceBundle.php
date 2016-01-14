<?php

namespace Tolerance\Bridge\Symfony\Bundle\ToleranceBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tolerance\Bridge\Symfony\Bundle\ToleranceBundle\DependencyInjection\CompilerPass\AopCompilerPass;
use Tolerance\Bridge\Symfony\Bundle\ToleranceBundle\DependencyInjection\CompilerPass\CollectOperationRunnersCompilerPass;

class ToleranceBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AopCompilerPass());
        $container->addCompilerPass(new CollectOperationRunnersCompilerPass());
    }
}
