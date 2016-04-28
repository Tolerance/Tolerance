<?php

namespace Tolerance\Bridge\Laravel\Illuminate\Support;

use PHPUnit\IlluminateTestCase;

class ServiceProviderTest extends IlluminateTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->app->register(DummyProvider::class);
    }

    public function test_register_service_without_instantiator()
    {
        $service = $this->app->make('injected_dummy');

        $this->assertInstanceOf(InjectedDummy::class, $service);
        $this->assertSame($service, $this->app->make(InjectedDummy::class));
    }

    public function test_register_service_with_instantiator()
    {
        $service = $this->app->make('dummy');

        $this->assertInstanceOf(Dummy::class, $service);
        $this->assertSame($service, $this->app->make(Dummy::class));
        $this->assertSame([$service], $this->app->tagged('dummy_tag'));
        $this->assertSame([$service], $this->app->tagged('another_dummy_tag'));
    }
}

class DummyProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerService('injected_dummy', InjectedDummy::class);

        $this->registerService(
            'dummy',
            Dummy::class,
            function ($app) {
                $injected = $app->make('injected_dummy');

                return new Dummy($injected);
            },
            ['dummy_tag', 'another_dummy_tag']
        );
    }
}

class Dummy
{
    public function __construct(InjectedDummy $x)
    {
    }
}

class InjectedDummy
{
}
