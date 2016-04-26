<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = new Fidry\LaravelYaml\Functional\App\Application(
    realpath(__DIR__)
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    \Fidry\LaravelYaml\Test\Foundation\ConsoleKernel::class
);
$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    Illuminate\Foundation\Exceptions\Handler::class
);

return $app;
