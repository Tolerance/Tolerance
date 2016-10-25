<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Tracer\Span;

class Span
{
    /**
     * @var Identifier
     */
    private $identifier;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Identifier
     */
    private $traceIdentifier;

    /**
     * @var Identifier|null
     */
    private $parentIdentifier;

    /**
     * @var Annotation[]
     */
    private $annotations;

    /**
     * @var BinaryAnnotation[]
     */
    private $binaryAnnotations;

    /**
     * @var bool|null
     */
    private $debug;

    /**
     * @var int|null
     */
    private $timestamp;

    /**
     * @var int|null
     */
    private $duration;

    /**
     * Span constructor.
     *
     * @param Identifier         $identifier
     * @param string             $name
     * @param Identifier         $traceIdentifier
     * @param Annotation[]       $annotations
     * @param BinaryAnnotation[] $binaryAnnotations
     * @param Identifier|null    $parentIdentifier
     * @param bool|null          $debug
     * @param int|null           $timestamp
     * @param int|null           $duration
     */
    public function __construct(Identifier $identifier, $name, Identifier $traceIdentifier, array $annotations = [], array $binaryAnnotations = [], Identifier $parentIdentifier = null, $debug = null, $timestamp = null, $duration = null)
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->traceIdentifier = $traceIdentifier;
        $this->parentIdentifier = $parentIdentifier;
        $this->annotations = $annotations;
        $this->binaryAnnotations = $binaryAnnotations;
        $this->debug = $debug;
        $this->timestamp = $timestamp;
        $this->duration = $duration;
    }

    /**
     * @return Identifier
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Identifier
     */
    public function getTraceIdentifier()
    {
        return $this->traceIdentifier;
    }

    /**
     * @return null|Identifier
     */
    public function getParentIdentifier()
    {
        return $this->parentIdentifier;
    }

    /**
     * @return Annotation[]
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    /**
     * @return BinaryAnnotation[]
     */
    public function getBinaryAnnotations()
    {
        return $this->binaryAnnotations;
    }

    /**
     * @return bool|null
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * @return int|null
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return int|null
     */
    public function getDuration()
    {
        return $this->duration;
    }
}
