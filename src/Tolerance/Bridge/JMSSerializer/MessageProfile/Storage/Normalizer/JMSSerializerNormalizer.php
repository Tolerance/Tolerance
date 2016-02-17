<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\JMSSerializer\MessageProfile\Storage\Normalizer;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Tolerance\MessageProfile\MessageProfile;
use Tolerance\MessageProfile\Storage\Normalizer\ProfileNormalizer;

class JMSSerializerNormalizer implements ProfileNormalizer
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var array
     */
    private $groups;

    /**
     * @param SerializerInterface $serializer
     * @param array               $groups
     */
    public function __construct(SerializerInterface $serializer, array $groups = ['Default', 'message-profile'])
    {
        $this->serializer = $serializer;
        $this->groups = $groups;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(MessageProfile $profile)
    {
        $context = SerializationContext::create()->setGroups($this->groups);
        $serialized = $this->serializer->serialize($profile, 'json', $context);

        return json_decode($serialized, true);
    }
}
