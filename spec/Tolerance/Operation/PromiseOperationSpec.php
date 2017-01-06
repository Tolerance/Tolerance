<?php

namespace spec\Tolerance\Operation;

use Tolerance\Operation\Operation;
use PhpSpec\ObjectBehavior;

class PromiseOperationSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(function() {});
    }

    function it_should_be_an_operation()
    {
        $this->shouldHaveType(Operation::class);
    }
}
