<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Symfony\MessageProfile;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Tolerance\MessageProfile\Identifier\Generator\MessageIdentifierGenerator;

class GenerateRequestIdentifierIfNotInHeaders
{
    /**
     * @var MessageIdentifierGenerator
     */
    private $messageIdentifierGenerator;

    /**
     * @var string
     */
    private $headerName;

    /**
     * @param MessageIdentifierGenerator $messageIdentifierGenerator
     * @param string                     $headerName
     */
    public function __construct(MessageIdentifierGenerator $messageIdentifierGenerator, $headerName)
    {
        $this->messageIdentifierGenerator = $messageIdentifierGenerator;
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

        $request->headers->set($this->headerName, (string) $this->messageIdentifierGenerator->generate());
    }
}
