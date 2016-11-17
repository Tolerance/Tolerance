<?php

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
