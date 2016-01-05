<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\RequestIdentifier\Storage;

use Tolerance\RequestIdentifier\RequestIdentifier;

class InMemoryRequestIdentifierStorage implements RequestIdentifierStorage
{
    /**
     * @var RequestIdentifier|null
     */
    private $identifier;

    /**
     * {@inheritdoc}
     */
    public function setRequestIdentifier(RequestIdentifier $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestIdentifier()
    {
        return $this->identifier;
    }
}
