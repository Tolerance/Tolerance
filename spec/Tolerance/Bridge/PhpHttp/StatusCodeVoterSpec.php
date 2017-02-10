<?php

namespace spec\Tolerance\Bridge\PhpHttp;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Tolerance\Operation\Exception\PromiseException;

class StatusCodeVoterSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([500, 502]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Tolerance\Bridge\PhpHttp\StatusCodeVoter');
    }

    function it_should_catch_if_not_promise_exception()
    {
        $this->callOnWrappedObject('shouldCatchThrowable', [new \RuntimeException()])->shouldReturn(true);
        $this->callOnWrappedObject('shouldCatchThrowable', [new \Exception()])->shouldReturn(true);
        $this->callOnWrappedObject('shouldCatchThrowable', [new \InvalidArgumentException()])->shouldReturn(true);

        // only test this on php >= 7.0
        if (70000 <= PHP_VERSION_ID) {
            $this->callOnWrappedObject('shouldCatchThrowable', [new \Error()])->shouldReturn(true);
        }
    }

    function it_should_catch_promise_exception_if_rejected(ResponseInterface $response)
    {
        $this->callOnWrappedObject('shouldCatchThrowable', [new PromiseException($response, false)])->shouldReturn(true);
    }

    function it_should_catch_promise_exception_if_value_not_instance_of_response()
    {
        $this->callOnWrappedObject('shouldCatchThrowable', [new PromiseException('value', true)])->shouldReturn(true);
    }

    function it_should_catch_promise_exception_if_value_is_a_response_and_status_code_matched(PromiseException $exception, ResponseInterface $response)
    {
        $response->getStatusCode()->willReturn(500);
        $exception->getValue()->willReturn($response);
        $exception->isRejected()->willReturn(false);
        $this->callOnWrappedObject('shouldCatchThrowable', [$exception])->shouldReturn(true);
    }

    function it_should_not_catch_promise_exception_if_value_is_a_response_and_status_code_dont_matched(PromiseException $exception, ResponseInterface $response)
    {
        $response->getStatusCode()->willReturn(400);
        $exception->getValue()->willReturn($response);
        $exception->isRejected()->willReturn(false);
        $this->callOnWrappedObject('shouldCatchThrowable', [$exception])->shouldReturn(false);
    }
}
