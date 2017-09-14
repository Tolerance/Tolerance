<?php

namespace spec\Tolerance\Operation\Exception;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;

class PromiseExceptionSpec extends ObjectBehavior
{
    function it_should_be_an_exception()
    {
        $this->beConstructedWith('anything');
        $this->shouldHaveType(\Exception::class);
    }

    function it_should_have_detailled_message_when_rejected_if_value_is_a_response(ResponseInterface $response)
    {
        $response->getStatusCode()->willReturn(504);
        $response->getReasonPhrase()->willReturn('Timeout');
        $this->beConstructedWith($response, false);
        $this->getMessage()->shouldBeLike('Request resulted in a `504 Timeout` response');
    }

    function it_should_have_detailled_message_when_rejected_if_value_is_an_exception()
    {
        $exception = new \Exception('You fail');
        $this->beConstructedWith($exception, false);
        $this->getMessage()->shouldBeLike('You fail');
    }

    function it_should_have_previous_exception_when_rejected_if_value_is_an_exception()
    {
        $exception = new \Exception('You fail');
        $this->beConstructedWith($exception, false);
        $this->getPrevious()->shouldBeLike($exception);
    }
}
