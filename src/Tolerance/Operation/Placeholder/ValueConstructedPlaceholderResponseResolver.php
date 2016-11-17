<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Operation\Placeholder;

use Tolerance\Operation\Operation;

class ValueConstructedPlaceholderResponseResolver implements PlaceholderResponseResolver
{
    /**
     * @var mixed|null
     */
    private $value;

    /**
     * @param mixed|null $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function createResponse(Operation $operation, $throwable)
    {
        return $this->value;
    }
}
