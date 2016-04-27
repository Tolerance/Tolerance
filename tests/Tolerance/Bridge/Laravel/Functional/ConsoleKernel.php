<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Laravel\Functional;

use Illuminate\Foundation\Console\Kernel;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class ConsoleKernel extends Kernel
{
    protected $commands = [];
}
