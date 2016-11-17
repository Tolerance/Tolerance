<?php

namespace spec\Tolerance\Bridge\Symfony\Operation;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tolerance\Bridge\Symfony\Operation\OperationRunnerRegistry;
use Tolerance\Operation\Runner\BufferedOperationRunner;

class RunBufferedOperationsWhenTerminatesSpec extends ObjectBehavior
{
    function let(OperationRunnerRegistry $registry)
    {
        $this->beConstructedWith($registry);
    }

    function it_is_an_event_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_susbscribe_to_the_kernel_terminate_event()
    {
        $this::getSubscribedEvents()->shouldHaveKey('kernel.terminate');
    }

    function it_susbscribe_to_the_console_terminate_event()
    {
        $this::getSubscribedEvents()->shouldHaveKey('console.terminate');
    }

    function it_runs_all_the_buffered_operations(BufferedOperationRunner $bufferedOperationRunner, OperationRunnerRegistry $registry)
    {
        $registry->findAllByClass(BufferedOperationRunner::class)->willReturn([$bufferedOperationRunner]);

        $bufferedOperationRunner->runBufferedOperations()->shouldBeCalled();

        $this->onTerminate();
    }
}
