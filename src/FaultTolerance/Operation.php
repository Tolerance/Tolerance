<?php

namespace FaultTolerance;

interface Operation
{
    /**
     * Run the given operation.
     *
     * @return mixed
     */
    public function run();
}
