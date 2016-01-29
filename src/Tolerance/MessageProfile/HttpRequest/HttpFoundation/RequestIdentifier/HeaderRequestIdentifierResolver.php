<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\MessageProfile\HttpRequest\HttpFoundation\RequestIdentifier;

use Symfony\Component\HttpFoundation\Request;
use Tolerance\MessageProfile\Identifier\Generator\MessageIdentifierGenerator;
use Tolerance\MessageProfile\Identifier\StringMessageIdentifier;

final class HeaderRequestIdentifierResolver implements RequestIdentifierResolver
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
    public function __construct(MessageIdentifierGenerator $identifierGenerator, $headerName)
    {
        $this->identifierGenerator = $identifierGenerator;
        $this->headerName = $headerName;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request)
    {
        if (null !== ($headerValue = $request->headers->get($this->headerName))) {
            return StringMessageIdentifier::fromString($headerValue);
        }

        return $this->identifierGenerator->generate();
    }
}
