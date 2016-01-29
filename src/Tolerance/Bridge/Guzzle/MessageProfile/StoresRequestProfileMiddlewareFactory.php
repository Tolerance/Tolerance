<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Guzzle\MessageProfile;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tolerance\MessageProfile\HttpRequest\Psr7\Psr7ProfileFactory;
use Tolerance\MessageProfile\Peer\Resolver\PeerResolver;
use Tolerance\MessageProfile\Storage\ProfileStorage;
use Tolerance\MessageProfile\Timing\MessageTiming;
use Tolerance\MessageProfile\Timing\SimpleMessageTiming;

final class StoresRequestProfileMiddlewareFactory
{
    /**
     * @var ProfileStorage
     */
    private $profileStorage;

    /**
     * @var Psr7ProfileFactory
     */
    private $profileFactory;

    /**
     * @var PeerResolver
     */
    private $peerResolver;

    /**
     * @param ProfileStorage     $profileStorage
     * @param Psr7ProfileFactory $profileFactory
     * @param PeerResolver       $peerResolver
     */
    public function __construct(ProfileStorage $profileStorage, Psr7ProfileFactory $profileFactory, PeerResolver $peerResolver)
    {
        $this->profileStorage = $profileStorage;
        $this->profileFactory = $profileFactory;
        $this->peerResolver = $peerResolver;
    }

    /**
     * @return callable
     */
    public function create()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $start = \DateTime::createFromFormat('U.u', microtime(true));

                return $handler($request, $options)->then(function (ResponseInterface $response) use ($start, $request) {
                    $end = \DateTime::createFromFormat('U.u', microtime(true));
                    $this->storeProfile($request, $response, SimpleMessageTiming::fromRange($start, $end));

                    return $response;
                }, function ($reason) use ($start, $request) {
                    $response = $reason instanceof RequestException ? $reason->getResponse() : null;

                    $end = \DateTime::createFromFormat('U.u', microtime(true));
                    $this->storeProfile($request, $response, SimpleMessageTiming::fromRange($start, $end));

                    throw $reason;
                });
            };
        };
    }

    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param MessageTiming     $timing
     */
    private function storeProfile(RequestInterface $request, ResponseInterface $response = null, MessageTiming $timing = null)
    {
        $sender = $this->peerResolver->resolve();
        $profile = $this->profileFactory->fromRequestAndResponse($request, $response, $sender);

        if (null !== $timing) {
            $profile = $profile->withTiming($timing);
        }

        $this->profileStorage->store($profile);
    }
}
