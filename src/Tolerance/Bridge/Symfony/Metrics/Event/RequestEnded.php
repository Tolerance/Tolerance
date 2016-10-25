<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Symfony\Metrics\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestEnded extends Event
{
    const EVENT_NAME = 'tolerance.request_ended';

    /**
     * @var Request
     */
    private $request;

    /**
     * @var null|Response
     */
    private $response;
    /**
     * @var null|\Exception|\Throwable
     */
    private $exception;

    /**
     * @param Request                    $request
     * @param Response|null              $response
     * @param \Exception|\Throwable|null $exception
     */
    public function __construct(Request $request, Response $response = null, $exception = null)
    {
        $this->request = $request;
        $this->response = $response;
        $this->exception = $exception;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return null|Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return \Exception|null|\Throwable
     */
    public function getException()
    {
        return $this->exception;
    }
}
