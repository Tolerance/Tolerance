<?php

namespace Tolerance\Bridge\Symfony\Bundle\AppBundle\Operation;

use Tolerance\Operation\Runner\CallbackOperationRunner;
use Tolerance\Operation\Runner\OperationRunner;
use Tolerance\Operation\Runner\RetryOperationRunner;
use Tolerance\Waiter\NullWaiter;
use Tolerance\Waiter\Strategy\Exponential;
use Tolerance\Waiter\Strategy\Max;

class ThirdPartyRunnerFactory
{
    /**
     * @return OperationRunner
     */
    public static function create()
    {
        return new RetryOperationRunner(
            new CallbackOperationRunner(),
            new Max(
                new Exponential(
                    new NullWaiter(),
                    1
                ),
                2
            )
        );
    }
}
