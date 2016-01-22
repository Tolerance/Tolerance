<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Throttling\Waiter;

use Tolerance\Throttling\RateLimit\RateLimit;
use Tolerance\Waiter\Waiter;

class RateLimitWaiter implements Waiter
{
    /**
     * @var RateLimit
     */
    private $rateLimit;

    /**
     * @var Waiter
     */
    private $waiter;

    /**
     * @param RateLimit $rateLimit
     * @param Waiter    $waiter
     */
    public function __construct(RateLimit $rateLimit, Waiter $waiter)
    {
        $this->rateLimit = $rateLimit;
        $this->waiter = $waiter;
    }

    /**
     * {@inheritdoc}
     */
    public function wait($identifier = '')
    {
        if ($this->rateLimit->hasReachedLimit($identifier)) {
            $this->waiter->wait(
                $this->rateLimit->getTicksBeforeUnderLimit($identifier)
            );
        }

        $this->rateLimit->tick($identifier);
    }
}
