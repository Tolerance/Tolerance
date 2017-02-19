<?php

namespace spec\Tolerance\Bridge\Guzzle\Operation\ExceptionCatcher;

use GuzzleHttp\Psr7\Response;
use PhpSpec\ObjectBehavior;
use Tolerance\Operation\Exception\PromiseException;
use Tolerance\Operation\ExceptionCatcher\ThrowableCatcherVoter;

class RequestServerErrorVoterSpec extends ObjectBehavior
{
    function it_is_an_exception_catcher_voter()
    {
        $this->shouldImplement(ThrowableCatcherVoter::class);
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

    function it_should_catch_promise_exception_if_rejected()
    {
        $this->callOnWrappedObject('shouldCatchThrowable', [new PromiseException(new Response(), false)])->shouldReturn(true);
    }

    function it_should_catch_promise_exception_if_value_not_instance_of_response()
    {
        $this->callOnWrappedObject('shouldCatchThrowable', [new PromiseException('value', true)])->shouldReturn(true);
    }

    function it_should_catch_promise_exception_if_value_is_a_response_and_server_error()
    {
        $this->callOnWrappedObject('shouldCatchThrowable', [new PromiseException(new Response(500), true)])->shouldReturn(true);
    }

    function it_should_not_catch_promise_exception_if_value_is_a_response_and_not_server_error()
    {
        $this->callOnWrappedObject('shouldCatchThrowable', [new PromiseException(new Response(400), true)])->shouldReturn(false);
    }
}
