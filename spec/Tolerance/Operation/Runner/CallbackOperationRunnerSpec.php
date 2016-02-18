<?php

namespace spec\Tolerance\Operation\Runner;

use Tolerance\Operation\Callback;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CallbackOperationRunnerSpec extends ObjectBehavior
{
    function it_should_call_the_callable(Callback $callback)
    {
        $callback->call()->shouldBeCalled();

        $this->run($callback);
    }

    function it_should_return_the_callable_result(Callback $callback)
    {
        $callback->call()->willReturn('foo');

        $this->run($callback)->shouldReturn('foo');
    }
}
