<?php

namespace Tolerance\Bridge\Symfony\Bundle\ToleranceBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Tolerance\Bridge\Symfony\Bundle\ToleranceBundle\DependencyInjection\CompilerPass\AopCompilerPass;

class ToleranceExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if ($config['request_identifier']['enabled']) {
            $container->setParameter('tolerance.request_identifier.header', $config['request_identifier']['header']);

            $loader->load('request-identifier/request.xml');
            $loader->load('request-identifier/listener.xml');

            if ($config['request_identifier']['monolog']) {
                $loader->load('request-identifier/monolog.xml');
            }
        }

        if ($config['aop']) {
            $container->addCompilerPass(new AopCompilerPass());
        }
    }
}
