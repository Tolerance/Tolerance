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

class InMemoryStorage implements ProfileStorage
{
    /**
     * @var MessageProfile[]
     */
    private $profiles;

    /**
     * {@inheritdoc}
     */
    public function store(MessageProfile $profile)
    {
        $this->profiles[] = $profile;
    }

    /**
     * @return \Tolerance\MessageProfile\MessageProfile[]
     */
    public function getProfiles()
    {
        return $this->profiles;
    }
}
