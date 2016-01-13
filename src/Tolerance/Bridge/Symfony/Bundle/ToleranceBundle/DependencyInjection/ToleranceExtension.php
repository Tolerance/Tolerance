<?php

namespace Tolerance\Bridge\Symfony\Bundle\ToleranceBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class ToleranceExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('tolerance.aop.enabled', $config['aop']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if ($config['request_identifier']['enabled']) {
            $this->loadRequestIdentifier($container, $loader, $config['request_identifier']);
        }

        if ($config['aop']) {
            $this->loadAop($container, $loader);
        }
    }

    private function loadRequestIdentifier(ContainerBuilder $container, LoaderInterface $loader, array $config)
    {
        $container->setParameter('tolerance.request_identifier.header', $config['header']);

        $loader->load('request-identifier/request.xml');
        $loader->load('request-identifier/listener.xml');

        if ($config['monolog']) {
            $loader->load('request-identifier/monolog.xml');
        }
    }

    private function loadAop(ContainerBuilder $container, LoaderInterface $loader)
    {
        $bundles = $container->getParameter('kernel.bundles');
        if (!array_key_exists('JMSAopBundle', $bundles)) {
            throw new \RuntimeException('You need to add the JMSAopBundle is you want to use the AOP feature');
        }

        $loader->load('aop.xml');
    }
}
