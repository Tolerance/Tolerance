<?php

namespace spec\Tolerance\Bridge\Symfony\Metrics\EventListener\KernelRequest;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RecordRequestStartTimeSpec extends ObjectBehavior
{
    function it_is_an_event_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_sets_the_request_attribute_from_the_request_time_float(GetResponseEvent $event, ParameterBagInterface $attributesParameterBag)
    {
        $request = new Request([], [], [], [], [], [
            'REQUEST_TIME_FLOAT' => '123456.789',
        ]);
        $request->attributes = $attributesParameterBag->getWrappedObject();
        $event->getRequest()->willReturn($request);

        $attributesParameterBag->set('_tolerance_request_time', 123456.789)->shouldBeCalled();

        $this->onRequest($event);
    }

    function it_sets_the_request_attribute_from_the_request_time(GetResponseEvent $event, ParameterBagInterface $attributesParameterBag)
    {
        $request = new Request([], [], [], [], [], [
            'REQUEST_TIME' => '123456',
        ]);
        $request->attributes = $attributesParameterBag->getWrappedObject();
        $event->getRequest()->willReturn($request);

        $attributesParameterBag->set('_tolerance_request_time', 123456.0)->shouldBeCalled();

        $this->onRequest($event);
    }

    function it_sets_the_request_attribute_from_the_current_time(GetResponseEvent $event, ParameterBagInterface $attributesParameterBag)
    {
        $request = new Request([], [], [], [], [], []);
        $request->attributes = $attributesParameterBag->getWrappedObject();
        $event->getRequest()->willReturn($request);

        $attributesParameterBag->set('_tolerance_request_time', Argument::type('float'))->shouldBeCalled();

        $this->onRequest($event);
    }
}
