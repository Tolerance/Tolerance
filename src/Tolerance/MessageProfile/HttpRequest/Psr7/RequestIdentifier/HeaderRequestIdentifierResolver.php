<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\MessageProfile\HttpRequest\Psr7\RequestIdentifier;

use Psr\Http\Message\RequestInterface;
use Tolerance\MessageProfile\Identifier\Generator\MessageIdentifierGenerator;
use Tolerance\MessageProfile\Identifier\StringMessageIdentifier;

class HeaderRequestIdentifierResolver implements RequestIdentifierResolver
{
    /**
     * @var MessageIdentifierGenerator
     */
    private $identifierGenerator;

    /**
     * @var string
     */
    private $headerName;

    /**
     * @param MessageIdentifierGenerator $identifierGenerator
     * @param string                     $headerName
     */
    public function __construct(MessageIdentifierGenerator $identifierGenerator, $headerName = 'X-Request-Id')
    {
        $this->identifierGenerator = $identifierGenerator;
        $this->headerName = $headerName;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(RequestInterface $request)
    {
        if ($request->hasHeader($this->headerName)) {
            return StringMessageIdentifier::fromString($request->getHeader($this->headerName[0]));
        }

        return $this->identifierGenerator->generate();
    }
}
