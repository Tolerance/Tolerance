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
     * @var string
     */
    private $identifier;

    /**
     * @var array
     */
    private $array;

    private function __construct()
    {
    }

    /**
     * @param array       $array
     * @param string|null $identifier
     *
     * @return ArbitraryPeer
     */
    public static function fromArray(array $array, $identifier = null)
    {
        $arbitraryPeer = new self();
        $arbitraryPeer->identifier = $identifier ?: md5(json_encode($array));
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

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
