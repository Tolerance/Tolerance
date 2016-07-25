<?php

namespace PHPUnit;

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\Kernel;

abstract class IlluminateTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * The Illuminate application instance.
     *
     * @var Application|null
     */
    protected $app;

    /**
     * @var ConsoleKernel|null
     */
    protected $kernel;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        if ($this->app === null) {
            $this->app = $this->createApplication();
        }
    }

    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        /* @var Application $app */
        $app = require __DIR__.'/../Tolerance/Bridge/Laravel/Functional/bootstrap.php';
        $this->kernel = $app->make(ConsoleKernel::class);
        /* @var Kernel $kernel */
        $this->kernel->bootstrap();

        return $app;
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    public function tearDown()
    {
        if (null !== $this->app) {
            $this->app->flush();
            $this->app = null;
            $this->kernel = null;
        }
    }

    /**
     * Asserts that the given service is an instance of the given class and bound to it.
     *
     * @param string $id    Service ID
     * @param string $class FQCN of the service
     */
    public function assertServiceIsRegistered($id, $class)
    {
        $service = $this->app->make($id);
        
        $this->assertInstanceOf($class, $service);
        $this->assertSame(
            $service,
            $this->app->make($class),
            sprintf('Expected class "%s" to be bound to the service "%s"', $class, $id)
        );
    }

    /**
     * Asserts that the list of given services are the exact list of services tagged with the given tag name.
     *
     * @param string   $tag      Tag name
     * @param string[] $services Expected services ids
     */
    public function assertTaggedServicesAreRegistered($tag, array $services)
    {
        $expected = [];
        foreach ($services as $serviceId) {
            $expected[] = $this->app->make($serviceId);
        }
        $this->assertSame(
            $expected,
            $this->app->tagged($tag)
        );
    }
}
