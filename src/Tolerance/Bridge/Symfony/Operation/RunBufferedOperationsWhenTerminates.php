<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Symfony\Operation;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Tolerance\Operation\Runner\BufferedOperationRunner;

class RunBufferedOperationsWhenTerminates implements EventSubscriberInterface
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

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::TERMINATE => ['onTerminate', -25],
            ConsoleEvents::TERMINATE => ['onTerminate', -25],
        ];
    }

    /**
     * Run all the buffered operations.
     *
     */
    public function onTerminate()
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
