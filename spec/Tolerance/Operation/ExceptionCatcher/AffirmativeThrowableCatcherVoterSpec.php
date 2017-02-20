<?php

namespace spec\Tolerance\Operation\ExceptionCatcher;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Operation\ExceptionCatcher\ThrowableCatcherVoter;

class AffirmativeThrowableCatcherVoterSpec extends ObjectBehavior
{
    function let(ThrowableCatcherVoter $firstVoter, ThrowableCatcherVoter $secondVoter)
    {
        $this->beConstructedWith([
            $firstVoter,
            $secondVoter
        ]);
    }

    function it_is_a_throwable_catcher_voter()
    {
        $this->shouldImplement(ThrowableCatcherVoter::class);
    }

    function it_vote_yes_if_one_of_the_voter_says_yes(ThrowableCatcherVoter $firstVoter, ThrowableCatcherVoter $secondVoter, \Exception $throwable)
    {
        $firstVoter->shouldCatchThrowable($throwable)->willReturn(false);
        $secondVoter->shouldCatchThrowable($throwable)->willReturn(true);

        $this->callOnWrappedObject('shouldCatchThrowable', [$throwable])->shouldReturn(true);
    }

    function it_vote_no_if_no_voter_says_yes(ThrowableCatcherVoter $firstVoter, ThrowableCatcherVoter $secondVoter, \Exception $throwable)
    {
        $firstVoter->shouldCatchThrowable($throwable)->willReturn(false);
        $secondVoter->shouldCatchThrowable($throwable)->willReturn(false);

        $this->callOnWrappedObject('shouldCatchThrowable', [$throwable])->shouldReturn(false);
    }
}
