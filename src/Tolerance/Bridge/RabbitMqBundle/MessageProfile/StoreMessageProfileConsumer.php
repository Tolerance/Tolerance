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

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Tolerance\MessageProfile\Identifier\Generator\MessageIdentifierGenerator;
use Tolerance\MessageProfile\Identifier\MessageIdentifier;
use Tolerance\MessageProfile\Identifier\StringMessageIdentifier;
use Tolerance\MessageProfile\Peer\Resolver\PeerResolver;
use Tolerance\MessageProfile\SimpleMessageProfile;
use Tolerance\MessageProfile\Storage\ProfileStorage;
use Tolerance\MessageProfile\Timing\SimpleMessageTiming;

class StoreMessageProfileConsumer implements ConsumerInterface
{
    /**
     * @var ConsumerInterface
     */
    private $decoratedConsumer;

    /**
     * @var MessageIdentifierGenerator
     */
    private $messageIdentifierGenerator;

    /**
     * @var ProfileStorage
     */
    private $profileStorage;

    /**
     * @var PeerResolver
     */
    private $currentPeerResolver;

    /**
     * @var string
     */
    private $headerName;

    /**
     * @param ConsumerInterface          $decoratedConsumer
     * @param ProfileStorage             $profileStorage
     * @param MessageIdentifierGenerator $messageIdentifierGenerator
     * @param PeerResolver               $currentPeerResolver
     * @param string                     $headerName
     */
    public function __construct(ConsumerInterface $decoratedConsumer, ProfileStorage $profileStorage, MessageIdentifierGenerator $messageIdentifierGenerator, PeerResolver $currentPeerResolver, $headerName)
    {
        $this->decoratedConsumer = $decoratedConsumer;
        $this->messageIdentifierGenerator = $messageIdentifierGenerator;
        $this->headerName = $headerName;
        $this->profileStorage = $profileStorage;
        $this->currentPeerResolver = $currentPeerResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(AMQPMessage $msg)
    {
        $start = microtime(true);

        $result = $this->decoratedConsumer->execute($msg);

        $profile = $this->generateProfile($msg);
        $profile = $profile->withTiming(SimpleMessageTiming::fromRange(
            \DateTime::createFromFormat('U.u', $start),
            \DateTime::createFromFormat('U.u', microtime(true))
        ));

        $this->profileStorage->store($profile);

        return $result;
    }

    /**
     * @param AMQPMessage $msg
     *
     * @return SimpleMessageProfile
     */
    private function generateProfile(AMQPMessage $msg)
    {
        return new SimpleMessageProfile(
            $this->getOrGenerateIdentifier($msg),
            $this->currentPeerResolver->resolve(),
            null,
            [
                'type' => 'amqp',
            ]
        );
    }

    /**
     * @param AMQPMessage $msg
     *
     * @return MessageIdentifier
     */
    private function getOrGenerateIdentifier(AMQPMessage $msg)
    {
        $headers = $msg->get('application_headers');
        if (array_key_exists($this->headerName, $headers)) {
            return StringMessageIdentifier::fromString($headers[$this->headerName]);
        }

        return $this->messageIdentifierGenerator->generate();
    }
}
