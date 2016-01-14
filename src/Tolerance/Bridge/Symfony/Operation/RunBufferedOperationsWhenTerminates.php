<?php

namespace Tolerance\Bridge\Symfony\Operation;

use Tolerance\Operation\Runner\BufferedOperationRunner;

class RunBufferedOperationsWhenTerminates
{
    /**
     * @var OperationRunnerRegistry
     */
    private $registry;

    /**
     * @param OperationRunnerRegistry $registry
     */
    public function __construct(OperationRunnerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function onKernelTerminate()
    {
        foreach ($this->getRunners() as $runner) {
            $runner->runBufferedOperations();
        }
    }

    /**
     * @return BufferedOperationRunner[]
     */
    private function getRunners()
    {
        return $this->registry->findAllByClass(BufferedOperationRunner::class);
    }
}
