<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\MessageProfile;

use Tolerance\MessageProfile\Identifier\MessageIdentifier;
use Tolerance\MessageProfile\Peer\MessagePeer;
use Tolerance\MessageProfile\Timing\MessageTiming;

class SimpleMessageProfile extends AbstractMessageProfile
{
    /**
     * @var MessageIdentifier
     */
    private $identifier;

    /**
     * @var MessagePeer
     */
    private $sender;

    /**
     * @var MessagePeer
     */
    private $recipient;

    /**
     * @var array
     */
    private $context;

    /**
     * @var MessageTiming
     */
    private $timing;

    /**
     * @var MessageIdentifier|null
     */
    private $parentIdentifier;

    /**
     * @param MessageIdentifier $identifier
     * @param MessagePeer       $sender
     * @param MessagePeer       $recipient
     * @param array             $context
     * @param MessageTiming     $timing
     */
    public function __construct(
        MessageIdentifier $identifier,
        MessagePeer $sender = null,
        MessagePeer $recipient = null,
        array $context = [],
        MessageTiming $timing = null
    ) {
        $this->identifier = $identifier;
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->context = $context;
        $this->timing = $timing;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * {@inheritdoc}
     */
    public function getTiming()
    {
        return $this->timing;
    }

    /**
     * {@inheritdoc}
     */
    public function withContext(array $context)
    {
        $profile = clone $this;
        $profile->context = $context;

        return $profile;
    }

    /**
     * {@inheritdoc}
     */
    public function withTiming(MessageTiming $timing)
    {
        $profile = clone $this;
        $profile->timing = $timing;

        return $profile;
    }

    /**
     * {@inheritdoc}
     */
    public function withRecipient(MessagePeer $recipient)
    {
        $profile = clone $this;
        $profile->recipient = $recipient;

        return $profile;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentIdentifier()
    {
        return $this->parentIdentifier;
    }

    /**
     * {@inheritdoc}
     */
    public function withParentIdentifier(MessageIdentifier $parentIdentifier)
    {
        $profile = clone $this;
        $profile->parentIdentifier = $parentIdentifier;

        return $profile;
    }
}
