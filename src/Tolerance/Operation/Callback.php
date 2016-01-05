<?php

namespace Tolerance\Operation;

use Tolerance\Operation\Operation;

class Callback implements Operation
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * @var mixed
     */
    private $result;

    /**
     * @param callable $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $callable = $this->callable;

        $this->result = $callable();

        return $this->result;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }
}
