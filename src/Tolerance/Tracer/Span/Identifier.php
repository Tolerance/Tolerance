<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Tracer\Span;

class Identifier
{
    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     */
    private function __construct($value)
    {
        $this->value = $value;
    }

    public static function fromString($string)
    {
        return new self($string);
    }

    public function __toString()
    {
        return (string) $this->value;
    }
}
