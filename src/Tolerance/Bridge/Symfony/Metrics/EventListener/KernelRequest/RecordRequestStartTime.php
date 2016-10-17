<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Symfony\Metrics\EventListener\KernelRequest;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class RecordRequestStartTime implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 254],
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (null !== ($requestTimeString = $request->server->get('REQUEST_TIME_FLOAT'))) {
            $requestTime = (float) $requestTimeString;
        } elseif (null !== ($requestTimeString = $request->server->get('REQUEST_TIME'))) {
            $requestTime = (float) $requestTimeString;
        } else {
            $requestTime = microtime(true);
        }

        $request->attributes->set('_tolerance_request_time', $requestTime);
    }
}
