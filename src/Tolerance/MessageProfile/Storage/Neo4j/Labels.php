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

final class Labels
{
    const MESSAGE = 'Message';

    const PEER = 'Peer';

    const SERVICE = 'Service';

    const HTTP_MESSAGE = 'HttpMessage';

    const AMQP_MESSAGE = 'AMQPMessage';
}
