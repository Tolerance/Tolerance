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

interface MessageContext
{
    /**
     * @return MessageIdentifier|null
     */
    public function getIdentifier();

    /**
     * @param MessageIdentifier $messageIdentifier
     */
    public function setIdentifier(MessageIdentifier $messageIdentifier);
}
