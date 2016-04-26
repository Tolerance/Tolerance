<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\Functional\App;

use Illuminate\Support\ServiceProvider;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class RegularServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->app->singleton(
            'dummies_chain',
            function (Application $application) {
                $dummies = $application->tagged('dummies');

                return new DummiesChain($dummies);
            }
        );
    }
}
