<?php

namespace spec\Tolerance\Operation\Runner;

use Tolerance\Operation\Buffer\InMemoryOperationBuffer;
use Tolerance\Operation\Operation;
use Tolerance\Operation\Buffer\OperationBuffer;
use Tolerance\Operation\Runner\OperationRunner;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BufferedOperationRunnerSpec extends ObjectBehavior
{
    function let(OperationRunner $runner, OperationBuffer $buffer)
    {
        $this->beConstructedWith($runner, $buffer);
    }

    function it_just_buffers_the_operations(OperationRunner $runner, Operation $operation, OperationBuffer $buffer)
    {
        $this->beConstructedWith($runner, $buffer);

        $buffer->add($operation)->shouldBeCalled();
        $runner->run($operation)->shouldNotBeCalled();

        $this->run($operation);
    }

    function it_runs_the_operations_already_in_the_buffer(OperationRunner $runner, Operation $first, Operation $second)
    {
        $buffer = new InMemoryOperationBuffer();
        $buffer->add($first->getWrappedObject());

        $this->beConstructedWith($runner, $buffer);
        $this->run($second);

        $this->runBufferedOperations();

        $runner->run($first)->shouldBeCalled();
        $runner->run($second)->shouldBeCalled();
    }
}
