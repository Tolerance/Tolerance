<?php

namespace Tolerance\Bridge\JMSAopBundle\Operation;

use CG\Proxy\MethodInterceptorInterface;
use CG\Proxy\MethodInvocation;
use Tolerance\Operation\Callback;

class WrapperInterceptor implements MethodInterceptorInterface
{
    /**
     * @var RunnerRepository
     */
    private $runnerRepository;

    /**
     * @param RunnerRepository $runnerRepository
     */
    public function __construct(RunnerRepository $runnerRepository)
    {
        $this->runnerRepository = $runnerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function intercept(MethodInvocation $invocation)
    {
        $operation = new Callback(function () use ($invocation) {
            return $invocation->proceed();
        });

        if (null === ($runner = $this->runnerRepository->getRunnerByMethod($invocation->reflection))) {
            return $operation->call();
        }

        $runner->run($operation);

        return $operation->getResult();
    }
}
