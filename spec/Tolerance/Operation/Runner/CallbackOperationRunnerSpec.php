<?php

namespace spec\Tolerance\Operation\Runner;

use Tolerance\Operation\Callback;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CallbackOperationRunnerSpec extends ObjectBehavior
{
    function it_should_call_the_callable(Callback $callback)
    {
        $callback->getCallable()->shouldBeCalled();

        $this->run(new Callback(function() use ($callback) {
            $callback->getWrappedObject()->getCallable();
        }));
    }

    function it_should_keep_track_of_the_result(Callback $operation)
    {
        $operation->getCallable()->willReturn(function() {
            return 'foo';
        });

        $operation->setState(Argument::any())->shouldBeCalled();
        $operation->setResult('foo')->shouldBeCalled();

        $this->run($operation);

    }
}
