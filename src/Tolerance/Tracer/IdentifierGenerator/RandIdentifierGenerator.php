<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Tracer\IdentifierGenerator;

use Tolerance\Tracer\Span\Identifier;

class RandIdentifierGenerator implements IdentifierGenerator
{
    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        return Identifier::fromString(
            (string) (rand() << 32 | rand())
        );
    }
}
