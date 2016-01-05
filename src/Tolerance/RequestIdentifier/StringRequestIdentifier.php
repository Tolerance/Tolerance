<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\RequestIdentifier;


class StringRequestIdentifier implements RequestIdentifier
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
     * @return StringRequestIdentifier
     */
    public static function fromString($string)
    {
        $identifier = new StringRequestIdentifier();
        $identifier->string = $string;

        return $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return $this->string;
    }
}
