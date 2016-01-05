<?php

namespace spec\Tolerance\Operation;

use Tolerance\Operation;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CallbackSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(function() {});
    }

    function it_should_be_an_operation()
    {
        $this->shouldHaveType(Operation::class);
    }

    function it_should_call_the_callable(Operation $operation)
    {
        $operation->run()->shouldBeCalled();

        $this->beConstructedWith(function() use ($operation) {
            $operation->getWrappedObject()->run();
        });

        $this->run();
    }

    function it_should_keep_track_of_the_result()
    {
        $this->beConstructedWith(function() {
            return 'foo';
        });

        $this->run();
        $this->getResult()->shouldReturn('foo');
    }
}
