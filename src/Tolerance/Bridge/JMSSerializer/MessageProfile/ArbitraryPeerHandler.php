<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\JMSSerializer\MessageProfile;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use Tolerance\MessageProfile\Peer\ArbitraryPeer;

class ArbitraryPeerHandler implements SubscribingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => ArbitraryPeer::class,
                'method' => 'serializeArbitraryPeer',
            ],
        ];
    }

    /**
     * Serialize a message identifier as a string.
     *
     * @param JsonSerializationVisitor $visitor
     *
     * @return string
     */
    public function serializeArbitraryPeer(JsonSerializationVisitor $visitor, ArbitraryPeer $arbitraryPeer, array $type, Context $context)
    {
        return $visitor->visitArray(
            $arbitraryPeer->getArray(),
            $type,
            $context
        );
    }
}
