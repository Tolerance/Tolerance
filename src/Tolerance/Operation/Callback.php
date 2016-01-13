<?php

namespace Tolerance\Operation;

class Callback extends StateContainedOperation implements Operation
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
     * @return mixed
     */
    public function call()
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
