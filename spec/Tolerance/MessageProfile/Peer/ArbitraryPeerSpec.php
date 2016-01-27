<?php

namespace spec\Tolerance\MessageProfile\Peer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\MessageProfile\Peer\MessagePeer;

class ArbitraryPeerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedFromArray([
            'name' => 'river',
        ]);
    }

    function it_is_a_message_peer()
    {
        $this->shouldImplement(MessagePeer::class);
    }

    function it_returns_the_array()
    {
        $this->getArray()->shouldBeLike([
            'name' => 'river',
        ]);
    }
}
