<?php

namespace spec\Tolerance\Bridge\JMSAopBundle\Operation;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Operation\Runner\OperationRunner;

class RunnerRepositorySpec extends ObjectBehavior
{
    function let(\ReflectionClass $class, \ReflectionMethod $method)
    {
        $class->getName()->willReturn('My\\Foo');
        $method->getDeclaringClass()->willReturn($class);
        $method->getName()->willReturn('aMethod');
    }

    function it_returns_the_runner_by_methods(\ReflectionMethod $method, OperationRunner $runner)
    {
        $this->addRunnerForMethod($method, $runner);
        $this->getRunnerByMethod($method)->shouldReturn($runner);
    }

    function it_returns_null_if_the_runner_was_not_added(\ReflectionMethod $method)
    {
        $this->getRunnerByMethod($method)->shouldReturn(null);
    }

    function it_returns_true_if_a_runner_was_added_for_the_class(\ReflectionMethod $method, \ReflectionClass $class, OperationRunner $runner)
    {
        $this->addRunnerForMethod($method, $runner);
        $this->hasRunnerForClass($class)->shouldReturn(true);
    }

    function it_returns_false_if_a_runner_was_added_for_a_different_class(\ReflectionMethod $method, \ReflectionClass $anotherClass, OperationRunner $runner)
    {
        $anotherClass->getName()->willReturn('My\\Bar');

        $this->addRunnerForMethod($method, $runner);
        $this->hasRunnerForClass($anotherClass)->shouldReturn(false);
    }

    function it_supports_adding_a_runner_with_class_and_method_path(\ReflectionClass $class, OperationRunner $runner)
    {
        $this->addRunnerAt('My\\Foo:aMethod', $runner);
        $this->hasRunnerForClass($class)->shouldReturn(true);
    }
}
