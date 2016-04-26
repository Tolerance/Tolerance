<?php

/**
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\Functional\App;

use Fidry\LaravelYaml\Test\AnotherDummy;
use Fidry\LaravelYaml\Test\DummyInterface;
use Fidry\LaravelYaml\Test\DummyService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class AppProvider extends ServiceProvider
{
    public function register()
    {
        $appClass = ('testing' === env('APP_ENV')) ? 'Fidry\LaravelYaml\Test\Foundation\ApplicationMock' : 'Fidry\LaravelYaml\Functional\App\Application';

        $this->app['env_val'] = env('APP_URL');
        $this->app['true_param'] = true;
        $this->app['int_param'] = 2000;
        $this->app['application.class'] = $appClass;
        $this->app['config_value'] = config('app.locale');
        $this->app['other_config_val'] = $this->app['application.class'];

        $this->app['service_param'] = 'yo';
        $this->app->instance('dummy', new DummyService());
        $this->app->bind(DummyService::class, 'dummy');

        $this->app->alias('dummy', 'foo');

        $this->app->singleton(
            'another_dummy',
            function (Application $app) {
                $serviceParam = $app['service_param'];

                try {
                    $inexistingService = $app['inexisting_service'];
                } catch (\Exception $exception) {
                    $inexistingService = null;
                }

                $dummy = $app['dummy'];

                return new AnotherDummy($serviceParam, $inexistingService, $dummy);
            }
        );
        $this->app->bind(AnotherDummy::class, 'another_dummy');
        $this->app->bind(DummyInterface::class, 'another_dummy');
        $this->app->tag('another_dummy', ['dummies', 'random_tag']);
    }
}
