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

final class DefaultIdentifierStrategy implements ThrottlingIdentifierStrategy
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @param string $identifier
     */
    public function __construct($identifier = '')
    {
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getOperationIdentifier(Operation $operation)
    {
        return $this->identifier;
    }
}
