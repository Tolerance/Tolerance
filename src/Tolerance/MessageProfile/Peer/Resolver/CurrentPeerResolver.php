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

use Tolerance\MessageProfile\Peer\ArbitraryPeer;

class CurrentPeerResolver implements PeerResolver
{
    /**
     * @var array
     */
    private $array;

    /**
     * @param array $array
     */
    public function __construct(array $array = [])
    {
        $this->array = $array;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve()
    {
        return ArbitraryPeer::fromArray($this->array);
    }

    /**
     * Adds the following data in the peer.
     *
     * @param array $array
     */
    public function merge(array $array)
    {
        $this->array = array_merge($this->array, $array);
    }
}
