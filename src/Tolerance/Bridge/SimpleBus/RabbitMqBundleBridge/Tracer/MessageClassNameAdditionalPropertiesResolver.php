<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\SimpleBus\RabbitMqBundleBridge\Tracer;

use PhpAmqpLib\Wire\AMQPTable;
use SimpleBus\Asynchronous\Properties\AdditionalPropertiesResolver;

class MessageClassNameAdditionalPropertiesResolver implements AdditionalPropertiesResolver
{
    /**
     * {@inheritdoc}
     */
    public function resolveAdditionalPropertiesFor($message)
    {
        $className = get_class($message);
        $name = substr($className, strrpos($className, '\\') + 1);

        return [
            'application_headers' => new AMQPTable([
                'name' => $name,
            ]),
        ];
    }
}
