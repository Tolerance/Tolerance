<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\MessageProfile\Storage\Neo4j;

final class RelationshipTypes
{
    const RECEIVED_BY = 'RECEIVED_BY';

    const SENT_BY = 'SENT_BY';

    const PARENT_MESSAGE = 'PARENT_MESSAGE';

    const PART_OF_SERVICE = 'PART_OF_SERVICE';
}
