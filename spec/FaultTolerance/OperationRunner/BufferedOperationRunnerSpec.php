<?php

namespace spec\FaultTolerance\OperationRunner;

use FaultTolerance\Operation;
use FaultTolerance\OperationBuffer;
use FaultTolerance\OperationRunner;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BufferedOperationRunnerSpec extends ObjectBehavior
{
    function let(OperationRunner $runner, OperationBuffer $buffer)
    {
        $this->beConstructedWith($runner, $buffer);
    }

    function it_runs_the_operations_already_in_the_buffer(OperationRunner $runner, Operation $first, Operation $second)
    {
        $buffer = new OperationBuffer\InMemoryOperationBuffer();
        $buffer->add($first->getWrappedObject());

        $this->beConstructedWith($runner, $buffer);
        $this->run($second);

        $runner->run($first)->shouldBeCalled();
        $runner->run($second)->shouldBeCalled();
    }

    function it_keeps_the_operation_in_the_buffer_if_it_fails(OperationBuffer $buffer, OperationRunner $runner, Operation $operation)
    {
        $runner->run($operation)->willThrow(new \RuntimeException('Unable to run the operation'));
        $buffer->current()->willReturn($operation);
        $this->shouldThrow(\RuntimeException::class)->duringRun($operation);
        $buffer->pop()->shouldNotBeCalled();
    }
}
