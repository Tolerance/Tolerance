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
     * @return callable
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }
}
