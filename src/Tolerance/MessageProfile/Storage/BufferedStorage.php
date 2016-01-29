<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\MessageProfile\Storage;

use Tolerance\MessageProfile\MessageProfile;

/**
 * This storage simply buffers the profiles and can send them when calling the
 * `flushProfiles` method.
 *
 * For instance, the Symfony Bridge has a listener on `kernel.terminates` that
 * flushes all the messages in the buffer.
 */
class BufferedStorage implements ProfileStorage
{
    /**
     * @var ProfileStorage
     */
    private $decorated;

    /**
     * @var MessageProfile[]
     */
    private $bufferedProfiles = [];

    /**
     * @param ProfileStorage $decorated
     */
    public function __construct(ProfileStorage $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * {@inheritdoc}
     */
    public function store(MessageProfile $profile)
    {
        $this->bufferedProfiles[] = $profile;
    }

    /**
     * Flush the profiles in the buffer.
     */
    public function flushProfiles()
    {
        foreach ($this->bufferedProfiles as $profile) {
            $this->decorated->store($profile);
        }
    }
}
