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
        $callback->setState(Argument::any())->shouldBeCalled();

        $this->run($callback);
    }
}
