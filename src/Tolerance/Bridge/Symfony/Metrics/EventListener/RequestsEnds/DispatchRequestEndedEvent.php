<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Symfony\Metrics\EventListener\RequestsEnds;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Tolerance\Bridge\Symfony\Events;
use Tolerance\Bridge\Symfony\Metrics\Event\RequestEnded;

class DispatchRequestEndedEvent implements EventSubscriberInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::TERMINATE => ['onTerminate', -10],
            KernelEvents::EXCEPTION => ['onException', -10],
        ];
    }

    /**
     * @param PostResponseEvent $event
     */
    public function onTerminate(PostResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->eventDispatcher->dispatch(Events::REQUEST_ENDS, new RequestEnded(
            $event->getRequest(),
            $event->getResponse()
        ));
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onException(GetResponseForExceptionEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->eventDispatcher->dispatch(Events::REQUEST_ENDS, new RequestEnded(
            $event->getRequest(),
            $event->getResponse(),
            $event->getException()
        ));
    }
}
