<?php

namespace spec\FaultTolerance\OperationRunner;

use FaultTolerance\Operation;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SimpleOperationRunnerSpec extends ObjectBehavior
{
    function it_should_run_an_operation(Operation $operation)
    {
        $operation->run()->shouldBeCalled();
        $this->run($operation);
    }
}
