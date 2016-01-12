<?php

namespace Tolerance\Bridge\Guzzle\RequestIdentifier;

use Psr\Http\Message\RequestInterface;
use Tolerance\RequestIdentifier\Resolver\RequestIdentifierResolver;

class MiddlewareFactory
{
    /**
     * @var RequestIdentifierResolver
     */
    private $resolver;

    /**
     * @var string
     */
    private $headerName;

    /**
     * @param RequestIdentifierResolver $resolver
     * @param string                    $headerName
     */
    public function __construct(RequestIdentifierResolver $resolver, $headerName)
    {
        $this->resolver = $resolver;
        $this->headerName = $headerName;
    }

    /**
     * @return callable
     */
    public function create()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $identifier = $this->resolver->get();

                $request = $request->withAddedHeader($this->headerName, $identifier->get());

                return $handler($request, $options);
            };
        };
    }
}
