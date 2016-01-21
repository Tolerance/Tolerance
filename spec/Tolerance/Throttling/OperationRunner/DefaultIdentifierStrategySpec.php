<?php

namespace spec\Tolerance\Throttling\OperationRunner;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Operation\Operation;
use Tolerance\Throttling\OperationRunner\ThrottlingIdentifierStrategy;

class DefaultIdentifierStrategySpec extends ObjectBehavior
{
    function it_is_a_throttling_identifier_strategy()
    {
        $this->shouldImplement(ThrottlingIdentifierStrategy::class);
    }

    function its_default_value_is_an_empty_string(Operation $operation)
    {
        $this->getOperationIdentifier($operation)->shouldReturn('');
    }

    function it_can_be_constructed_with_the_default_value(Operation $operation)
    {
        $this->beConstructedWith('my-default');

        $this->getOperationIdentifier($operation)->shouldReturn('my-default');
    }
}
