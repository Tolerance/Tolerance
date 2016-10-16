<?php

use Beberlei\Bundle\MetricsBundle\BeberleiMetricsBundle;
use Csa\Bundle\GuzzleBundle\CsaGuzzleBundle;
use JMS\AopBundle\JMSAopBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Tolerance\Bridge\Symfony\Bundle\AppBundle\AppBundle;
use Tolerance\Bridge\Symfony\Bundle\ToleranceBundle\ToleranceBundle;

class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new JMSAopBundle(),
            new ToleranceBundle(),
            new CsaGuzzleBundle(),
            new BeberleiMetricsBundle(),
            new AppBundle(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.yml');
    }
}
