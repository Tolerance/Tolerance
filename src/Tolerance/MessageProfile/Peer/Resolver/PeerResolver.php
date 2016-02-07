<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\MessageProfile\Peer\Resolver;

use Tolerance\MessageProfile\Peer\MessagePeer;

interface PeerResolver
{
    /**
     * @return MessagePeer
     */
    public function resolve();
}
