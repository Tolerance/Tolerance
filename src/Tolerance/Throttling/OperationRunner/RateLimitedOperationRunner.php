<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Throttling\OperationRunner;

use Tolerance\Operation\Operation;
use Tolerance\Operation\Runner\OperationRunner;
use Tolerance\Throttling\RateLimit\RateLimit;
use Tolerance\Waiter\Waiter;

final class RateLimitedOperationRunner implements OperationRunner
{
    /**
     * @var OperationRunner
     */
    private $runner;
    /**
     * @var RateLimit
     */
    private $rateLimit;
    /**
     * @var Waiter
     */
    private $waiter;
    /**
     * @var ThrottlingIdentifierStrategy
     */
    private $identifierStrategy;

    /**
     * @param OperationRunner              $runner
     * @param RateLimit                    $rateLimit
     * @param Waiter                       $waiter
     * @param ThrottlingIdentifierStrategy $identifierStrategy
     */
    public function __construct(OperationRunner $runner, RateLimit $rateLimit, Waiter $waiter, ThrottlingIdentifierStrategy $identifierStrategy = null)
    {
        $this->runner = $runner;
        $this->rateLimit = $rateLimit;
        $this->waiter = $waiter;
        $this->identifierStrategy = $identifierStrategy ?: new DefaultIdentifierStrategy();
    }

    /**
     * {@inheritdoc}
     */
    public function run(Operation $operation)
    {
        $identifier = $this->identifierStrategy->getOperationIdentifier($operation);

        if ($this->rateLimit->hasReachedLimit($identifier)) {
            $this->waiter->wait(
                $this->rateLimit->getTicksBeforeUnderLimit($identifier)
            );
        }

        $this->rateLimit->tick($identifier);

        return $this->runner->run($operation);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Operation $operation)
    {
        return $this->runner->supports($operation);
    }
}
