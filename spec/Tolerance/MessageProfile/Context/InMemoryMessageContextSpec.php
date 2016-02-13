<?php

namespace spec\Tolerance\MessageProfile\Context;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\MessageProfile\Context\MessageContext;
use Tolerance\MessageProfile\Identifier\MessageIdentifier;

class InMemoryMessageContextSpec extends ObjectBehavior
{
    function it_is_a_message_context_implementation()
    {
        $this->shouldImplement(MessageContext::class);
    }

    function it_returns_null_by_default()
    {
        $this->getIdentifier()->shouldReturn(null);
    }

    function it_returns_the_message_identifier(MessageIdentifier $messageIdentifier)
    {
        $this->setIdentifier($messageIdentifier);
        $this->getIdentifier()->shouldReturn($messageIdentifier);
    }
}
