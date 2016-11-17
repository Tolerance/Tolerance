<?php

namespace spec\Tolerance\Operation\Placeholder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Operation\Operation;
use Tolerance\Operation\Placeholder\PlaceholderResponseResolver;

class ValueConstructedPlaceholderResponseResolverSpec extends ObjectBehavior
{
    function it_is_a_placeholder_response_resolver()
    {
        $this->shouldImplement(PlaceholderResponseResolver::class);
    }

    function it_returns_null_by_default(Operation $operation, \Exception $e)
    {
        $this->createResponse($operation, $e)->shouldBe(null);
    }

    function it_returns_the_value_it_was_constructed_with(Operation $operation, \Exception $e)
    {
        $this->beConstructedWith('continuouspipe.io');

        $this->createResponse($operation, $e)->shouldBe('continuouspipe.io');
    }
}
