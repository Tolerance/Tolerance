<?php

namespace Tolerance\Bridge\Symfony\MessageProfile;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Tolerance\MessageProfile\HttpRequest\HttpFoundation\RequestIdentifier\RequestIdentifierResolver;

class GenerateRequestIdentifierIfNotInHeaders
{
    /**
     * @var RequestIdentifierResolver
     */
    private $requestIdentifierResolver;

    /**
     * @var string
     */
    private $headerName;

    /**
     * @param RequestIdentifierResolver $requestIdentifierResolver
     * @param string $headerName
     */
    public function __construct(RequestIdentifierResolver $requestIdentifierResolver, $headerName)
    {
        $this->requestIdentifierResolver = $requestIdentifierResolver;
        $this->headerName = $headerName;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if ($request->headers->has($this->headerName)) {
            return;
        }

        $identifier = (string) $this->requestIdentifierResolver->resolve($request);
        $request->headers->set($this->headerName, $identifier);
    }
}
