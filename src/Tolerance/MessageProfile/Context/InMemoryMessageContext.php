<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\MessageProfile\Context;

use Tolerance\MessageProfile\Identifier\MessageIdentifier;

final class InMemoryMessageContext implements MessageContext
{
    /**
     * @var MessageIdentifier|null
     */
    private $messageIdentifier;

    /**
     * {@inheritdoc}
     */
    public function setIdentifier(MessageIdentifier $messageIdentifier)
    {
        $this->messageIdentifier = $messageIdentifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->messageIdentifier;
    }
}
