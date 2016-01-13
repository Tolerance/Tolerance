<?php

namespace Tolerance\Bridge\Symfony\Bundle\ToleranceBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tolerance\Bridge\Symfony\Bundle\ToleranceBundle\DependencyInjection\CompilerPass\AopCompilerPass;

class ToleranceBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AopCompilerPass());
    }
}
