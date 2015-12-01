<?php

namespace spec\FaultTolerance\OperationBuffer;

use FaultTolerance\Operation;
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
        $this->get()->shouldReturn($operation);
    }

    function it_returns_the_first_operation_added_first(Operation $first, Operation $second)
    {
        $this->add($first);
        $this->add($second);

        $this->get()->shouldReturn($first);
        $this->get()->shouldReturn($second);
    }
}
