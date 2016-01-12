<?php

namespace Tolerance\Operation;

interface Operation
{
    /**
     * Is the operation successful?
     *
     * @return bool
     */
    public function isSuccessful();

    /**
     * Is the operation failed?
     *
     * @return bool
     */
    public function isFailed();

    /**
     * Returns true if the operation was run.
     *
     * @return bool
     */
    public function hasRun();
}
