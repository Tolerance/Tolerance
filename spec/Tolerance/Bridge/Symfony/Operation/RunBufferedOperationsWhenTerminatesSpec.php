<?php

namespace spec\Tolerance\Bridge\Symfony\Operation;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Bridge\Symfony\Operation\OperationRunnerRegistry;
use Tolerance\Operation\Runner\BufferedOperationRunner;

class RunBufferedOperationsWhenTerminatesSpec extends ObjectBehavior
{
    function it_runs_all_the_buffered_operations(BufferedOperationRunner $bufferedOperationRunner, OperationRunnerRegistry $registry)
    {
        $this->beConstructedWith($registry);
        $registry->findAllByClass(BufferedOperationRunner::class)->willReturn([$bufferedOperationRunner]);

        $bufferedOperationRunner->runBufferedOperations()->shouldBeCalled();

        $this->onKernelTerminate();
    }
}
