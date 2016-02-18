<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

        return $runner->run($operation);
    }
}
