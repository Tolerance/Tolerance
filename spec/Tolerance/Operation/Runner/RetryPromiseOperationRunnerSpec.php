<?php

namespace spec\Tolerance\Operation\Runner;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\RejectedPromise;
use GuzzleHttp\Promise\RejectionException;
use Tolerance\Operation\PromiseOperation;
use Tolerance\Operation\Runner\OperationRunner;
use Tolerance\Waiter\StatefulWaiter;
use Tolerance\Waiter\WaiterException;
use Tolerance\Waiter\Waiter;
use PhpSpec\ObjectBehavior;

class RetryPromiseOperationRunnerSpec extends ObjectBehavior
{
    function let(Waiter $waiter)
    {
        $this->beConstructedWith($waiter);
    }

    function it_should_be_an_operation_runner()
    {
        $this->shouldHaveType(OperationRunner::class);
    }

    function it_should_retry_to_run_the_operation_until_success(PromiseOperation $operation)
    {
        $operation->getPromise()
            ->shouldBeCalledTimes(4)
            ->willReturn(new RejectedPromise('1st fail'), new RejectedPromise('2nd fail'), new RejectedPromise('3rd fail'), new FulfilledPromise('Success'))
        ;

        $promise = $this->run($operation)->shouldHaveType(Promise::class);
        $value = $promise->wait();

        if ('Success' !== $value) {
            throw new \Exception(sprintf('Value "%s" expcted, got "%s"', 'Success', $value));
        }
    }

    function it_should_retry_to_run_the_operation_until_waiter_exception(PromiseOperation $operation, Waiter $waiter)
    {
        $waiter->wait()->will(function () use ($waiter) {
            $waiter->wait()->willThrow(new WaiterException()); // Exception will be thrown at the 2nd call of wait()
        });
        $this->beConstructedWith($waiter);

        $operation->getPromise()
            ->shouldBeCalledTimes(2)
            ->willReturn(new RejectedPromise('fail'), new RejectedPromise('fail'), new RejectedPromise('fail'))
        ;

        $promise = $this->run($operation)->shouldHaveType(Promise::class);

        $exception = null;
        try {
            $promise->wait();
        } catch (\Exception $exception) {
        }

        if (!$exception instanceof RejectionException) {
            throw new \Exception(sprintf('Expected exception "%s", got "%s"', RejectionException::class, $exception ? get_class($exception) : 'null'));
        }
    }

    function it_should_reset_the_state_of_any_stateful_waiter(PromiseOperation $operation, StatefulWaiter $statefulWaiter)
    {
        $this->beConstructedWith($statefulWaiter);

        $statefulWaiter->resetState()->shouldBeCalled();
        $operation->getPromise()->willReturn(new FulfilledPromise('Success'));

        $promise = $this->run($operation)->shouldHaveType(Promise::class);
        $promise->wait();
    }

    function it_should_reset_the_state_only_once(PromiseOperation $operation, StatefulWaiter $statefulWaiter)
    {
        $this->beConstructedWith($statefulWaiter);

        $statefulWaiter->resetState()->shouldBeCalledTimes(1);
        $statefulWaiter->wait()->willReturn(null);

        $operation->getPromise()
            ->shouldBeCalledTimes(4)
            ->willReturn(new RejectedPromise('1st fail'), new RejectedPromise('2nd fail'), new RejectedPromise('3rd fail'), new FulfilledPromise('Success'))
        ;

        $promise = $this->run($operation)->shouldHaveType(Promise::class);
        $promise->wait();
    }
}
