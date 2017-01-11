<?php

namespace spec\Tolerance\Operation\ExceptionCatcher;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Operation\ExceptionCatcher\ExceptionCatcherVoter;
use Tolerance\Operation\ExceptionCatcher\ThrowableCatcherVoter;

class IgnoreExceptionVoterSpec extends ObjectBehavior
{
    function it_is_an_exception_catcher_voter()
    {
        $this->shouldImplement(ThrowableCatcherVoter::class);
    }

    function it_should_not_catch_any_exception()
    {
        $this->callOnWrappedObject('shouldCatch', [new \RuntimeException()])->shouldReturn(false);
        $this->callOnWrappedObject('shouldCatch', [new \Exception()])->shouldReturn(false);
        $this->callOnWrappedObject('shouldCatch', [new \InvalidArgumentException()])->shouldReturn(false);
    }

    function it_should_not_catch_on_any_throwable()
    {
        $this->callOnWrappedObject('shouldCatchThrowable', [new \RuntimeException()])->shouldReturn(false);
        $this->callOnWrappedObject('shouldCatchThrowable', [new \Exception()])->shouldReturn(false);
        $this->callOnWrappedObject('shouldCatchThrowable', [new \InvalidArgumentException()])->shouldReturn(false);

        // only test this on php >= 7.0
        if (70000 <= PHP_VERSION_ID) {
            $this->callOnWrappedObject('shouldCatchThrowable', [new \Error()])->shouldReturn(false);
        }
    }

    function it_should_not_catch_on_not_throwables()
    {
        $this->callOnWrappedObject('shouldCatchThrowable', ['foo'])->shouldReturn(false);
        $this->callOnWrappedObject('shouldCatchThrowable', [[]])->shouldReturn(false);
        $this->callOnWrappedObject('shouldCatchThrowable', [(object) 'foo'])->shouldReturn(false);
    }
}
