<?php

namespace spec\Tolerance\Bridge\RabbitMqBundle\MessageProfile;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\MessageProfile\Context\MessageContext;
use Tolerance\MessageProfile\Identifier\Generator\MessageIdentifierGenerator;
use Tolerance\MessageProfile\Identifier\StringMessageIdentifier;
use Tolerance\MessageProfile\MessageProfile;
use Tolerance\MessageProfile\Peer\Resolver\PeerResolver;
use Tolerance\MessageProfile\Storage\ProfileStorage;

class StoreMessageProfileProducerSpec extends ObjectBehavior
{
    function let(ProducerInterface $producer, ProfileStorage $profileStorage, MessageIdentifierGenerator $messageIdentifierGenerator, MessageContext $messageContext, PeerResolver $peerResolver)
    {
        $this->beConstructedWith($producer, $profileStorage, $messageIdentifierGenerator, $peerResolver, $messageContext, 'x-message-id');

        $messageIdentifierGenerator->generate()->willReturn(StringMessageIdentifier::fromString('1234'));
    }

    function it_is_a_producer()
    {
        $this->shouldImplement(ProducerInterface::class);
    }

    function it_calls_the_decorated_producer(ProducerInterface $producer)
    {
        $producer->publish('', '', Argument::any())->shouldBeCalled();

        $this->publish('', '');
    }

    function it_stores_the_message_profile(ProfileStorage $profileStorage)
    {
        $profileStorage->store(Argument::type(MessageProfile::class))->shouldBeCalled();

        $this->publish('', '');
    }

    function it_generates_a_message_identifier_if_no_found_in_headers(MessageIdentifierGenerator $messageIdentifierGenerator, ProfileStorage $profileStorage)
    {
        $messageIdentifierGenerator->generate()->shouldBeCalled();

        $profileStorage->store(Argument::that(function(MessageProfile $profile) {
            return ((string) $profile->getIdentifier()) === '1234';
        }))->shouldBeCalled();

        $this->publish('', '', []);
    }

    function it_uses_the_message_identifier_in_headers_if_found(ProfileStorage $profileStorage)
    {
        $profileStorage->store(Argument::that(function(MessageProfile $profile) {
            return ((string) $profile->getIdentifier()) === '09876';
        }))->shouldBeCalled();

        $this->publish('', '', [
            'application_headers' => [
                'x-message-id' => '09876',
            ],
        ]);
    }
}
