<?php

namespace spec\Tolerance\Operation\Runner;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Tolerance\Operation\ExceptionCatcher\ThrowableCatcherVoter;
use Tolerance\Operation\Operation;
use Tolerance\Operation\Placeholder\PlaceholderResponseResolver;
use Tolerance\Operation\Runner\OperationRunner;

class PlaceholderOperationRunnerSpec extends ObjectBehavior
{
    function let(OperationRunner $decoratedRunner, PlaceholderResponseResolver $placeholderResponseResolver)
    {
        $this->beConstructedWith($decoratedRunner, $placeholderResponseResolver);
    }

    function it_is_an_operation_runner()
    {
        $this->shouldImplement(OperationRunner::class);
    }

    function it_supports_what_the_decorated_runner_supports(OperationRunner $decoratedRunner, Operation $firstOperation, Operation $secondOperation)
    {
        $decoratedRunner->supports($firstOperation)->willReturn(true);
        $decoratedRunner->supports($secondOperation)->willReturn(false);

        $this->supports($firstOperation)->shouldBe(true);
        $this->supports($secondOperation)->shouldBe(false);
    }

    function it_returns_what_the_runner_returns_in_case_of_success(OperationRunner $decoratedRunner, Operation $operation)
    {
        $decoratedRunner->run($operation)->willReturn([true]);

        $this->run($operation)->shouldReturn([true]);
    }

    function it_returns_a_placeholder_response_if_it_fails(OperationRunner $decoratedRunner, Operation $operation, PlaceholderResponseResolver $placeholderResponseResolver)
    {
        $exception = new \Exception();
        $decoratedRunner->run($operation)->willThrow($exception);

        $placeholderResponseResolver->createResponse($operation, $exception)->shouldBeCalled()->willReturn('');

        $this->run($operation)->shouldBe('');
    }

    function it_catches_only_if_the_optional_catcher_voter_agrees(OperationRunner $decoratedRunner, PlaceholderResponseResolver $placeholderResponseResolver, Operation $operation, ThrowableCatcherVoter $catcherVoter)
    {
        $this->beConstructedWith($decoratedRunner, $placeholderResponseResolver, $catcherVoter);

        $exception = new \Exception();
        $decoratedRunner->run($operation)->willThrow($exception);

        $catcherVoter->shouldCatchThrowable($exception)->willReturn(false);

        $this->shouldThrow($exception)->duringRun($operation);
    }

    function it_warns_the_logger_if_any_when_failed(OperationRunner $decoratedRunner, Operation $operation, PlaceholderResponseResolver $placeholderResponseResolver, LoggerInterface $logger)
    {
        $this->beConstructedWith($decoratedRunner, $placeholderResponseResolver, null, $logger);

        $exception = new \Exception();
        $decoratedRunner->run($operation)->willThrow($exception);

        $placeholderResponseResolver->createResponse($operation, $exception)->shouldBeCalled()->willReturn('');
        $logger->warning(Argument::type('string'), Argument::type('array'))->shouldBeCalled();

        $this->run($operation)->shouldBe('');
    }
}
