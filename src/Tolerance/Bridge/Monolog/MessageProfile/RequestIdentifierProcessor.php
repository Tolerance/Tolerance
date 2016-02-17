<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Monolog\MessageProfile;

use Symfony\Component\HttpFoundation\RequestStack;
use Tolerance\MessageProfile\HttpRequest\HttpFoundation\RequestIdentifier\RequestIdentifierResolver;

final class RequestIdentifierProcessor
{
    /**
     * @var RequestIdentifierResolver
     */
    private $requestIdentifierResolver;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestIdentifierResolver $requestIdentifierResolver
     * @param RequestStack              $requestStack
     */
    public function __construct(RequestIdentifierResolver $requestIdentifierResolver, RequestStack $requestStack)
    {
        $this->requestIdentifierResolver = $requestIdentifierResolver;
        $this->requestStack = $requestStack;
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
        if (null === ($request = $this->requestStack->getCurrentRequest())) {
            return $record;
        }

        $record['context']['tags'] = [
            'request-identifier' => (string) $this->requestIdentifierResolver->resolve($request),
        ];

        return $record;
    }
}
