<?php

namespace spec\Tolerance\MessageProfile;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\MessageProfile\Identifier\MessageIdentifier;
use Tolerance\MessageProfile\MessageProfile;

class SimpleMessageProfileSpec extends ObjectBehavior
{
    function let(MessageIdentifier $messageIdentifier)
    {
        $this->beConstructedWith($messageIdentifier);
    }

    function it_is_a_message_profile()
    {
        $this->shouldImplement(MessageProfile::class);
    }
}
