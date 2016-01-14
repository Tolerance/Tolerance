<?php

namespace Tolerance\Bridge\Monolog\RequestIdentifier;

use Tolerance\RequestIdentifier\Resolver\RequestIdentifierResolver;

class RequestIdentifierProcessor
{
    /**
     * @var RequestIdentifierResolver
     */
    private $requestIdentifierResolver;

    /**
     * @param RequestIdentifierResolver $requestIdentifierResolver
     */
    public function __construct(RequestIdentifierResolver $requestIdentifierResolver)
    {
        $this->requestIdentifierResolver = $requestIdentifierResolver;
    }

    /**
     * Updates the record with the request identifier.
     *
     * @param array $record
     *
     * @return array
     */
    public function __invoke(array $record)
    {
        $record['context']['tags'] = [
            'request-identifier' => (string) $this->requestIdentifierResolver->resolve(),
        ];

        return $record;
    }
}
