<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\MessageProfile\Peer;

final class ArbitraryPeer implements MessagePeer
{
    /**
     * @var array
     */
    private $array;

    private function __construct()
    {
    }

    /**
     * @param array $array
     *
     * @return ArbitraryPeer
     */
    public static function fromArray(array $array)
    {
        $arbitraryPeer = new self();
        $arbitraryPeer->array = $array;

        return $arbitraryPeer;
    }

    /**
     * @return array
     */
    public function getArray()
    {
        return $this->array;
    }
}
