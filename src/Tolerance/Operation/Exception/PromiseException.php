<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Operation\Exception;

class PromiseException extends \Exception
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var bool
     */
    private $fulfilled;

    public function __construct($value, $fulfilled = true)
    {
        $this->value = $value;
        $this->fulfilled = $fulfilled;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isFulfilled()
    {
        return $this->fulfilled;
    }

    /**
     * @return bool
     */
    public function isRejected()
    {
        return !$this->fulfilled;
    }
}
