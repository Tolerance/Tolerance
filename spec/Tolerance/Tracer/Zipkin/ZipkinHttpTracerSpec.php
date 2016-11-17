<?php

namespace spec\Tolerance\Tracer\Zipkin;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Tolerance\Tracer\IdentifierGenerator\RandIdentifierGenerator;
use Tolerance\Tracer\Span\Span;
use Tolerance\Tracer\Tracer;
use Tolerance\Tracer\TracerException;

class ZipkinHttpTracerSpec extends ObjectBehavior
{
    function let(ClientInterface $client)
    {
        $this->beConstructedWith($client, 'http://example.com');
    }

    function it_is_a_tracer()
    {
        $this->shouldImplement(Tracer::class);
    }

    function it_sends_a_post_request_to_the_spans_api(ClientInterface $client)
    {
        $client->request('POST', 'http://example.com/api/v1/spans', Argument::type('array'))->shouldBeCalled();

        $this->trace([
            $this->generateTrace(),
        ]);
    }

    function it_throws_a_TracerException_if_anything_goes_wrong(ClientInterface $client, RequestInterface $request)
    {
        $exception = new RequestException('Message', $request->getWrappedObject());

        if (version_compare(ClientInterface::VERSION, '6.0') >= 0) {
            $client->request('POST', Argument::any(), Argument::any())->willThrow($exception);
        } else {
            $client->post(Argument::type('string'), Argument::type('array'))->willThrow($exception);
        }

        $this->shouldThrow(TracerException::class)->duringTrace([
            $this->generateTrace(),
        ]);
    }

    /**
     * @return Span
     */
    private function generateTrace()
    {
        $identifier = (new RandIdentifierGenerator())->generate();

        return new Span($identifier, 'Name', $identifier);
    }
}
