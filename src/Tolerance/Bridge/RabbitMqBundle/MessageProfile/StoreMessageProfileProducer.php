<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\RabbitMqBundle\MessageProfile;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Tolerance\MessageProfile\Context\MessageContext;
use Tolerance\MessageProfile\Identifier\Generator\MessageIdentifierGenerator;
use Tolerance\MessageProfile\Identifier\MessageIdentifier;
use Tolerance\MessageProfile\Identifier\StringMessageIdentifier;
use Tolerance\MessageProfile\Peer\Resolver\PeerResolver;
use Tolerance\MessageProfile\SimpleMessageProfile;
use Tolerance\MessageProfile\Storage\ProfileStorage;
use Tolerance\MessageProfile\Timing\SimpleMessageTiming;

class StoreMessageProfileProducer implements ProducerInterface
{
    /**
     * @var ProducerInterface
     */
    private $decoratedProducer;

    /**
     * @var ProfileStorage
     */
    private $profileStorage;

    /**
     * @var MessageIdentifierGenerator
     */
    private $messageIdentifierGenerator;

    /**
     * @var PeerResolver
     */
    private $currentPeerResolver;

    /**
     * @var MessageContext
     */
    private $messageContext;

    /**
     * @var string
     */
    private $headerName;

    /**
     * @param ProducerInterface          $decoratedProducer
     * @param ProfileStorage             $profileStorage
     * @param MessageIdentifierGenerator $messageIdentifierGenerator
     * @param PeerResolver               $currentPeerResolver
     * @param MessageContext             $messageContext
     * @param string                     $headerName
     */
    public function __construct(ProducerInterface $decoratedProducer, ProfileStorage $profileStorage, MessageIdentifierGenerator $messageIdentifierGenerator, PeerResolver $currentPeerResolver, MessageContext $messageContext, $headerName)
    {
        $this->decoratedProducer = $decoratedProducer;
        $this->profileStorage = $profileStorage;
        $this->headerName = $headerName;
        $this->messageIdentifierGenerator = $messageIdentifierGenerator;
        $this->currentPeerResolver = $currentPeerResolver;
        $this->messageContext = $messageContext;
    }

    /**
     * {@inheritdoc}
     */
    public function publish($msgBody, $routingKey = '', $additionalProperties = array())
    {
        $start = microtime(true);

        $this->decoratedProducer->publish($msgBody, $routingKey, $additionalProperties);

        $profile = $this->generateProfile($additionalProperties);
        $profile = $profile->withTiming(SimpleMessageTiming::fromRange(
            \DateTime::createFromFormat('U.u', $start),
            \DateTime::createFromFormat('U.u', microtime(true))
        ));

        $this->profileStorage->store($profile);
    }

    /**
     * @param array $additionalProperties
     *
     * @return SimpleMessageProfile
     */
    private function generateProfile(array $additionalProperties)
    {
        $profile = new SimpleMessageProfile(
            $this->getOrGenerateIdentifier($additionalProperties),
            $this->currentPeerResolver->resolve(),
            null,
            [
                'type' => 'amqp',
            ]
        );

        if (null !== ($parentIdentifier = $this->messageContext->getIdentifier())) {
            $profile = $profile->withParentIdentifier($parentIdentifier);
        }

        return $profile;
    }

    /**
     * @param array $additionalProperties
     *
     * @return MessageIdentifier
     */
    private function getOrGenerateIdentifier(array $additionalProperties)
    {
        if (array_key_exists('application_headers', $additionalProperties)) {
            $headers = $additionalProperties['application_headers'];

            if (array_key_exists($this->headerName, $headers)) {
                return StringMessageIdentifier::fromString($headers[$this->headerName]);
            }
        }

        return $this->messageIdentifierGenerator->generate();
    }
}
