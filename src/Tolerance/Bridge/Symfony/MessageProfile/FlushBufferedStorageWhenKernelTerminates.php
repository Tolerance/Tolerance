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
use Tolerance\MessageProfile\Storage\BufferedStorage;

class FlushBufferedStorageWhenKernelTerminates
{
    /**
     * @var BufferedStorage
     */
    private $bufferedStorage;

    /**
     * @param BufferedStorage $bufferedStorage
     */
    public function __construct(BufferedStorage $bufferedStorage)
    {
        $this->bufferedStorage = $bufferedStorage;
    }

    /**
     * @param PostResponseEvent $event
     */
    public function onKernelTerminate(PostResponseEvent $event)
    {
        $this->bufferedStorage->flushProfiles();
    }
}
