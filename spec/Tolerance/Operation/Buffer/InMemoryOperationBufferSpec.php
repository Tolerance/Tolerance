<?php

namespace spec\Tolerance\Operation\Buffer;

use Tolerance\Operation\Operation;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InMemoryOperationBufferSpec extends ObjectBehavior
{
    function it_adds_operations_to_its_buffer(Operation $operation)
    {
        $this->add($operation);
    }

    function it_returns_an_operation_previously_added(Operation $operation)
    {
        $this->add($operation);
        $this->current()->shouldReturn($operation);
    }

    function it_always_returns_the_same_operation_when_calling_current(Operation $first, Operation $second)
    {
        $this->add($first);
        $this->add($second);

        $this->current()->shouldReturn($first);
        $this->current()->shouldReturn($first);
    }

    function it_returns_the_first_operation_added_first(Operation $first, Operation $second)
    {
        $this->add($first);
        $this->add($second);

        $this->current()->shouldReturn($first);
        $this->pop();
        $this->current()->shouldReturn($second);
    }
}
