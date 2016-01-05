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
        $identifier = new StringRequestIdentifier();
        $identifier->string = $string;

        return $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return $this->string;
    }
}
