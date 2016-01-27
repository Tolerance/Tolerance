<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\MessageProfile\Identifier;

class StringMessageIdentifier implements MessageIdentifier
{
    /**
     * @var string
     */
    private $string;

    private function __construct()
    {
    }

    /**
     * @param string $string
     *
     * @return StringMessageIdentifier
     */
    public static function fromString($string)
    {
        $identifier = new self();
        $identifier->string = $string;

        return $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->string;
    }
}
