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

use Psr\Http\Message\ResponseInterface;

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

        if (false === $this->fulfilled) {
            if ($value instanceof ResponseInterface) {
                $message = sprintf(
                    'Request resulted in a `%s %s` response',
                    $value->getStatusCode(),
                    $value->getReasonPhrase()
                );
            } elseif ($value instanceof \Exception) {
                $message = $value->getMessage();
                $previous = $value;
            } else {
                $message = !is_object($value) ? (string) $value : sprintf('Something went wrong (%s)', get_class($value));
            }

            parent::__construct($message, 0, isset($previous) ? $previous : null);
        }
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
