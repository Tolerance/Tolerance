<?php

namespace spec\Tolerance\Operation\ExceptionCatcher;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Operation\ExceptionCatcher\ExceptionCatcherVoter;

class WildcardExceptionVoterSpec extends ObjectBehavior
{
    function it_is_an_exception_catcher_voter()
    {
        $this->shouldImplement(ExceptionCatcherVoter::class);
    }

    function it_should_catch_any_exception()
    {
        $this->callOnWrappedObject('shouldCatch', [new \RuntimeException()])->shouldReturn(true);
        $this->callOnWrappedObject('shouldCatch', [new \Exception()])->shouldReturn(true);
        $this->callOnWrappedObject('shouldCatch', [new \InvalidArgumentException()])->shouldReturn(true);
    }
}
