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

use Tolerance\MessageProfile\MessageProfile;
use Tolerance\MessageProfile\Timing\MessageTiming;

final class HttpMessageProfile implements MessageProfile
{
    /**
     * @var MessageProfile
     */
    private $decorated;

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
     * @param MessageProfile $decorated
     * @param string         $method
     * @param string         $path
     * @param int|null       $statusCode
     */
    public function __construct(MessageProfile $decorated, $method, $path, $statusCode = null)
    {
        $this->decorated = $decorated;
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

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->decorated->getIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function getSender()
    {
        return $this->decorated->getSender();
    }

    /**
     * {@inheritdoc}
     */
    public function getRecipient()
    {
        return $this->decorated->getRecipient();
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->decorated->getContext();
    }

    /**
     * {@inheritdoc}
     */
    public function getTiming()
    {
        return $this->decorated->getTiming();
    }

    /**
     * {@inheritdoc}
     */
    public function withMergedContext(array $context)
    {
        return $this->with($this->decorated->withMergedContext($context));
    }

    /**
     * {@inheritdoc}
     */
    public function withTiming(MessageTiming $timing)
    {
        return $this->with($this->decorated->withTiming($timing));
    }

    /**
     * @param MessageProfile $decorated
     *
     * @return HttpMessageProfile
     */
    private function with(MessageProfile $decorated)
    {
        $profile = clone $this;
        $profile->decorated = $decorated;

        return $profile;
    }
}
