<?php

namespace Tolerance\RequestIdentifier;

class StringRequestIdentifier implements RequestIdentifier
{
    /**
     * @var string
     */
    private $string;

    private function __construct()
    {
    }

    /**
     * @param string $string
     *
     * @return StringRequestIdentifier
     */
    public static function fromString($string)
    {
        $identifier = new self();
        $identifier->string = $string;

        return $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->string;
    }
}
