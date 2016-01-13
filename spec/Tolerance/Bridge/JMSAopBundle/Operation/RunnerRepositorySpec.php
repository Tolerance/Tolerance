<?php

namespace spec\Tolerance\Bridge\JMSAopBundle\Operation;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Operation\Runner\CallbackOperationRunner;
use Tolerance\Operation\Runner\OperationRunner;
use Tolerance\Operation\Runner\RetryOperationRunner;

class RunnerRepositorySpec extends ObjectBehavior
{
    function it_returns_the_runner_by_methods(OperationRunner $runner)
    {
        $class = new \ReflectionClass(CallbackOperationRunner::class);
        $method = $class->getMethod('run');

        $this->addRunnerForMethod($method, $runner);
        $this->getRunnerByMethod($method)->shouldReturn($runner);
    }

    function it_returns_null_if_the_runner_was_not_added()
    {
        $class = new \ReflectionClass(CallbackOperationRunner::class);
        $method = $class->getMethod('run');

        $this->getRunnerByMethod($method)->shouldReturn(null);
    }

    function it_returns_true_if_a_runner_was_added_for_the_class(OperationRunner $runner)
    {
        $class = new \ReflectionClass(CallbackOperationRunner::class);
        $method = $class->getMethod('run');

        $this->addRunnerForMethod($method, $runner);
        $this->hasRunnerForClass($class)->shouldReturn(true);
    }

    function it_returns_false_if_a_runner_was_added_for_a_different_class(OperationRunner $runner)
    {
        $class = new \ReflectionClass(CallbackOperationRunner::class);
        $method = $class->getMethod('run');

        $anotherClass = new \ReflectionClass(RetryOperationRunner::class);

        $this->addRunnerForMethod($method, $runner);
        $this->hasRunnerForClass($anotherClass)->shouldReturn(false);
    }

    function it_supports_adding_a_runner_with_class_and_method_path(\ReflectionClass $class, OperationRunner $runner)
    {
        $class = new \ReflectionClass(CallbackOperationRunner::class);

        $this->addRunnerAt(CallbackOperationRunner::class.':run', $runner);
        $this->hasRunnerForClass($class)->shouldReturn(true);
    }
}
