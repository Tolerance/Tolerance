<?php

namespace spec\Tolerance\Operation\Runner;

use Tolerance\Operation\Operation;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CallbackOperationRunnerSpec extends ObjectBehavior
{
    function it_should_run_an_operation(Operation $operation)
    {
        $operation->run()->shouldBeCalled();
        $this->run($operation);
    }
}
