<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Symfony\RequestProfile;

use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Tolerance\MessageProfile\HttpRequest\HttpFoundation\HttpFoundationProfileFactory;
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
     * @param HttpFoundationProfileFactory $profileFactory
     * @param ProfileStorage               $profileStorage
     */
    public function __construct(HttpFoundationProfileFactory $profileFactory, ProfileStorage $profileStorage)
    {
        $this->profileFactory = $profileFactory;
        $this->profileStorage = $profileStorage;
    }

    /**
     * @param PostResponseEvent $event
     */
    public function onKernelTerminate(PostResponseEvent $event)
    {
        $profile = $this->profileFactory->fromRequestAndResponse($event->getRequest(), $event->getResponse());

        $this->profileStorage->store($profile);
    }
}
