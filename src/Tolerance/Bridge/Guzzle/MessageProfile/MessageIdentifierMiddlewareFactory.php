<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Guzzle\MessageProfile;

use Psr\Http\Message\RequestInterface;
use Tolerance\MessageProfile\Identifier\Generator\MessageIdentifierGenerator;

class MessageIdentifierMiddlewareFactory
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
     * @return callable
     */
    public function create()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $request = $request->withAddedHeader($this->headerName, (string) $this->identifierGenerator->generate());

                return $handler($request, $options);
            };
        };
    }
}
