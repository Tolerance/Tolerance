<?php

namespace Tolerance\Bridge\Laravel\Provider\Operation;

use Tolerance\Bridge\Laravel\Illuminate\Support\ServiceProvider;

final class AopProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerService(
            'tolerance.aop.runner_repository',
            \Tolerance\Bridge\JMSAopBundle\Operation\RunnerRepository::class
        );

        $this->registerService(
            'tolerance.aop.pointcut.interceptor',
            \Tolerance\Bridge\JMSAopBundle\Operation\WrapperInterceptor::class,
            function ($app) {
                /* @var \Tolerance\Bridge\JMSAopBundle\Operation\RunnerRepository $runnerRepository */
                $runnerRepository = $app->make('tolerance.aop.runner_repository');

                return new \Tolerance\Bridge\JMSAopBundle\Operation\WrapperInterceptor(
                    $runnerRepository
                );
            }
        );

        $this->registerService(
            'tolerance.aop.pointcut',
            \Tolerance\Bridge\JMSAopBundle\Operation\WrapperPointcut::class,
            function ($app) {
                /* @var \Tolerance\Bridge\JMSAopBundle\Operation\WrapperPointcut $runnerRepository */
                $runnerRepository = $app->make('tolerance.aop.runner_repository');

                return new \Tolerance\Bridge\JMSAopBundle\Operation\WrapperInterceptor(
                    $runnerRepository
                );
            }
        );
    }
}
