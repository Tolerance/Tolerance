<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\MessageProfile\HttpRequest;

use Tolerance\MessageProfile\Identifier\MessageIdentifier;
use Tolerance\MessageProfile\Peer\MessagePeer;
use Tolerance\MessageProfile\SimpleMessageProfile;
use Tolerance\MessageProfile\Timing\MessageTiming;

class HttpMessageProfile extends SimpleMessageProfile
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $path;

    /**
     * @var int|null
     */
    private $statusCode;

    /**
     * @param MessageIdentifier $identifier
     * @param MessagePeer       $sender
     * @param MessagePeer       $recipient
     * @param array             $context
     * @param MessageTiming     $timing
     * @param string            $method
     * @param string            $path
     * @param int               $statusCode
     */
    public function __construct(
        MessageIdentifier $identifier,
        MessagePeer $sender = null,
        MessagePeer $recipient = null,
        array $context = [],
        MessageTiming $timing = null,
        $method = null,
        $path = null,
        $statusCode = null
    ) {
        parent::__construct($identifier, $sender, $recipient, $context, $timing);

        $this->method = $method;
        $this->path = $path;
        $this->statusCode = $statusCode;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return int|null
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
