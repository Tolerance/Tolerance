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

        $this->profileStorage->store($profile);
    }
}
