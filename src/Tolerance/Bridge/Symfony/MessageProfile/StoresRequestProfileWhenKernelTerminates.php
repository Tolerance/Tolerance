<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Symfony\MessageProfile;

use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Tolerance\MessageProfile\HttpRequest\HttpFoundation\HttpFoundationProfileFactory;
use Tolerance\MessageProfile\Peer\Resolver\PeerResolver;
use Tolerance\MessageProfile\Storage\ProfileStorage;
use Tolerance\MessageProfile\Timing\SimpleMessageTiming;

final class StoresRequestProfileWhenKernelTerminates
{
    /**
     * @var HttpFoundationProfileFactory
     */
    private $profileFactory;

    /**
     * @var ProfileStorage
     */
    private $profileStorage;

    /**
     * @var PeerResolver
     */
    private $peerResolver;

    /**
     * @param HttpFoundationProfileFactory $profileFactory
     * @param ProfileStorage               $profileStorage
     * @param PeerResolver                 $peerResolver
     */
    public function __construct(HttpFoundationProfileFactory $profileFactory, ProfileStorage $profileStorage, PeerResolver $peerResolver)
    {
        $this->profileFactory = $profileFactory;
        $this->profileStorage = $profileStorage;
        $this->peerResolver = $peerResolver;
    }

    /**
     * @param PostResponseEvent $event
     */
    public function onKernelTerminate(PostResponseEvent $event)
    {
        $receiver = $this->peerResolver->resolve();

        $profile = $this->profileFactory->fromRequestAndResponse($event->getRequest(), $event->getResponse(), null, $receiver);
        $profile = $profile->withTiming($this->generateTiming());

        $this->profileStorage->store($profile);
    }

    /**
     * @return SimpleMessageTiming
     */
    private function generateTiming()
    {
        $start = array_key_exists('REQUEST_TIME_FLOAT', $_SERVER) ?
            \DateTime::createFromFormat('U.u', (float) $_SERVER['REQUEST_TIME_FLOAT']) :
            (array_key_exists('REQUEST_TIME', $_SERVER) ?
                \DateTime::createFromFormat('U', (int) $_SERVER['REQUEST_TIME']) :
                new \DateTime()
            )
        ;

        $end = \DateTime::createFromFormat('U.u', microtime(true));

        return SimpleMessageTiming::fromRange($start, $end);
    }
}
