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

use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use Tolerance\MessageProfile\Identifier\MessageIdentifier;
use Tolerance\MessageProfile\Identifier\StringMessageIdentifier;

class MessageIdentifierHandler implements SubscribingHandlerInterface
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
                'type' => StringMessageIdentifier::class,
                'method' => 'serializeMessageIdentifier',
            ],
        ];
    }

    /**
     * Serialize a message identifier as a string.
     *
     * @param JsonSerializationVisitor $visitor
     * @param MessageIdentifier        $identifier
     *
     * @return string
     */
    public function serializeMessageIdentifier(JsonSerializationVisitor $visitor, MessageIdentifier $identifier)
    {
        return (string) $identifier;
    }
}
