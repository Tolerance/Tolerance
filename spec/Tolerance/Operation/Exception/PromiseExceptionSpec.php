<?php

namespace spec\Tolerance\Operation\Exception;

use PhpSpec\ObjectBehavior;

class PromiseExceptionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('anything');
    }

    function it_should_be_an_operation()
    {
        $this->shouldHaveType(\Exception::class);
    }
}
