<?php

namespace spec\Tolerance\Bridge\JMSAopBundle\Operation;

use CG\Proxy\MethodInterceptorInterface;
use CG\Proxy\MethodInvocation;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Bridge\JMSAopBundle\Operation\RunnerRepository;
use Tolerance\Operation\Runner\OperationRunner;

class WrapperInterceptorSpec extends ObjectBehavior
{
    function let(RunnerRepository $runnerRepository)
    {
        $this->beConstructedWith($runnerRepository);
    }

    function it_is_an_interceptor()
    {
        $this->shouldImplement(MethodInterceptorInterface::class);
    }

    function it_calls_the_method_without_any_runner(RunnerRepository $runnerRepository, \ReflectionMethod $method, MethodInvocation $invocation)
    {
        $invocation->proceed()->shouldBeCalled();
        $invocation->reflection = $method;

        $runnerRepository->getRunnerByMethod($method)->willReturn(null);

        $this->intercept($invocation);
    }

    function it_calls_the_operation_runner_if_found(RunnerRepository $runnerRepository, \ReflectionMethod $method, MethodInvocation $invocation, OperationRunner $operationRunner)
    {
        $invocation->reflection = $method;
        $runnerRepository->getRunnerByMethod($method)->willReturn($operationRunner);
        $operationRunner->run(Argument::any())->shouldBeCalled();

        $this->intercept($invocation);
    }
}
