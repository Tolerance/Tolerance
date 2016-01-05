<?php

namespace Tolerance\Bridge\Symfony\RequestIdentifier;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Tolerance\RequestIdentifier\Storage\RequestIdentifierStorage;
use Tolerance\RequestIdentifier\StringRequestIdentifier;

class RequestHeadersListener
{
    /**
     * @var RequestIdentifierStorage
     */
    private $storage;

    /**
     * @var string
     */
    private $headerName;

    /**
     * @param RequestIdentifierStorage $storage
     * @param string                   $headerName
     */
    public function __construct(RequestIdentifierStorage $storage, $headerName)
    {
        $this->storage = $storage;
        $this->headerName = $headerName;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (null === ($contents = $request->headers->get($this->headerName))) {
            return;
        }

        $this->storage->setRequestIdentifier(
            StringRequestIdentifier::fromString($contents)
        );
    }
}
