<?php

namespace spec\Tolerance\Bridge\Symfony\Operation;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Operation\Runner\BufferedOperationRunner;
use Tolerance\Operation\Runner\OperationRunner;
use Tolerance\Operation\Runner\RetryOperationRunner;

class OperationRunnerRegistrySpec extends ObjectBehavior
{
    function it_returns_an_empty_array_if_not_operation_runner_added()
    {
        $this->findAllByClass(OperationRunner::class)->shouldReturn([]);
    }

    function it_returns_the_operation_runners_of_a_given_type(RetryOperationRunner $retryOperationRunner)
    {
        $this->registerOperationRunner($retryOperationRunner);

        $this->findAllByClass(RetryOperationRunner::class)->shouldReturn([$retryOperationRunner]);
    }

    function it_returns_all_the_operation_runner_of_a_type(BufferedOperationRunner $firstBufferedOperationRunner, BufferedOperationRunner $secondBufferedOperationRunner)
    {
        $this->registerOperationRunner($firstBufferedOperationRunner);
        $this->registerOperationRunner($secondBufferedOperationRunner);

        $this->findAllByClass(BufferedOperationRunner::class)->shouldReturn([$firstBufferedOperationRunner, $secondBufferedOperationRunner]);
    }
}
