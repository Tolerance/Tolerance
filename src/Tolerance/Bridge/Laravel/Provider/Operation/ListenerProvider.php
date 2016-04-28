<?php

namespace Tolerance\Bridge\Laravel\Provider\Operation;

use Tolerance\Bridge\Laravel\Illuminate\Support\ServiceProvider;

final class ListenerProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerService(
            'tolerance.operation_runner_registry',
            \Tolerance\Bridge\Symfony\Operation\OperationRunnerRegistry::class
        );

        $this->registerService(
            'tolerance.operation_runner_listeners.buffered_termination',
            \Tolerance\Bridge\Symfony\Operation\RunBufferedOperationsWhenTerminates::class,
            function ($app) {
                /* @var \Tolerance\Bridge\Symfony\Operation\OperationRunnerRegistry $runnerRegistry */
                $runnerRegistry = $app->make('tolerance.operation_runner_registry');

                return new \Tolerance\Bridge\Symfony\Operation\RunBufferedOperationsWhenTerminates(
                    $runnerRegistry
                );
            }
        );
    }
}
