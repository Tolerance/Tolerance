<?php

namespace spec\Tolerance\Bridge\RabbitMqBundle\MessageProfile;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\MessageProfile\Identifier\Generator\MessageIdentifierGenerator;
use Tolerance\MessageProfile\Identifier\StringMessageIdentifier;
use Tolerance\MessageProfile\MessageProfile;
use Tolerance\MessageProfile\Peer\Resolver\PeerResolver;
use Tolerance\MessageProfile\Storage\ProfileStorage;

class StoreMessageProfileConsumerSpec extends ObjectBehavior
{
    function let(ConsumerInterface $consumer, ProfileStorage $profileStorage, MessageIdentifierGenerator $messageIdentifierGenerator, PeerResolver $peerResolver, AMQPMessage $message)
    {
        $this->beConstructedWith($consumer, $profileStorage, $messageIdentifierGenerator, $peerResolver, 'x-message-id');

        $message->get('application_headers')->willReturn([]);
        $messageIdentifierGenerator->generate()->willReturn(StringMessageIdentifier::fromString('1234'));
    }

    function it_is_a_consumer()
    {
        $this->shouldImplement(ConsumerInterface::class);
    }

    function it_calls_the_decorated_consomer(ConsumerInterface $consumer, AMQPMessage $message)
    {
        $consumer->execute($message)->shouldBeCalled();

        $this->execute($message);
    }

    function it_stores_the_profile_of_the_message(ProfileStorage $profileStorage, AMQPMessage $message)
    {
        $profileStorage->store(Argument::type(MessageProfile::class))->shouldBeCalled();

        $this->execute($message);
    }

    function it_generates_a_message_identifier_if_not_found_in_message(MessageIdentifierGenerator $messageIdentifierGenerator, AMQPMessage $message)
    {
        $messageIdentifierGenerator->generate()->shouldBeCalled();

        $this->execute($message);
    }

    function it_uses_the_message_identifier_if_is_in_the_headers(ProfileStorage $profileStorage, AMQPMessage $message)
    {
        $message->get('application_headers')->willReturn(['x-message-id' => '9876']);
        $profileStorage->store(Argument::that(function(MessageProfile $profile) {
            return ((string) $profile->getIdentifier()) === '9876';
        }))->shouldBeCalled();

        $this->execute($message);
    }
}
