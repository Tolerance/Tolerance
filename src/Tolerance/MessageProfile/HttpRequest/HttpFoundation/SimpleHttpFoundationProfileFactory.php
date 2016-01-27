<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\MessageProfile\HttpRequest\HttpFoundation;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tolerance\MessageProfile\HttpRequest\HttpFoundation\RequestIdentifier\RequestIdentifierResolver;
use Tolerance\MessageProfile\HttpRequest\HttpMessageProfile;
use Tolerance\MessageProfile\Peer\ArbitraryPeer;
use Tolerance\MessageProfile\SimpleMessageProfile;
use Tolerance\MessageProfile\Timing\SimpleMessageTiming;

final class SimpleHttpFoundationProfileFactory implements HttpFoundationProfileFactory
{
    /**
     * @var RequestIdentifierResolver
     */
    private $requestIdentifierResolver;

    /**
     * @param RequestIdentifierResolver $requestIdentifierResolver
     */
    public function __construct(RequestIdentifierResolver $requestIdentifierResolver)
    {
        $this->requestIdentifierResolver = $requestIdentifierResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function fromRequestAndResponse(Request $request, Response $response)
    {
        return new HttpMessageProfile(
            new SimpleMessageProfile(
                $this->requestIdentifierResolver->resolve($request),
                $this->getSender(),
                null,
                $this->getContext(),
                $this->generateTiming()
            ),
            $request->getMethod(),
            $request->getRequestUri(),
            $response->getStatusCode()
        );
    }

    /**
     * @return ArbitraryPeer
     */
    private function getSender()
    {
        return ArbitraryPeer::fromArray([]);
    }

    /**
     * @return array
     */
    private function getContext()
    {
        return [];
    }

    /**
     * @return SimpleMessageTiming
     */
    private function generateTiming()
    {
        $start = array_key_exists('REQUEST_TIME_FLOAT', $_SERVER)
            ? \DateTime::createFromFormat('U.u', (string) $_SERVER['REQUEST_TIME_FLOAT'])
            : array_key_exists('REQUEST_TIME', $_SERVER)
                ? \DateTime::createFromFormat('U', (string) $_SERVER['REQUEST_TIME'])
                : new \DateTime();

        $end = \DateTime::createFromFormat('U.u', microtime(true));

        $difference = ((double) $end->format('U.u')) - ((double) $start->format('U.u'));

        return SimpleMessageTiming::fromMilliseconds($difference * 1000);
    }
}
