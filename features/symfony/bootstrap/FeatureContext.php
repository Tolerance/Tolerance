<?php

use Behat\Behat\Context\Context;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Tolerance\Operation\Callback;
use Tolerance\Operation\Runner\BufferedOperationRunner;

class FeatureContext implements Context
{
    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var BufferedOperationRunner
     */
    private $bufferedOperationRunner;

    /**
     * @var mixed
     */
    private $response;

    /**
     * @param Kernel                  $kernel
     * @param BufferedOperationRunner $bufferedOperationRunner
     */
    public function __construct(Kernel $kernel, BufferedOperationRunner $bufferedOperationRunner)
    {
        $this->kernel = $kernel;
        $this->bufferedOperationRunner = $bufferedOperationRunner;
    }

    /**
     * @Given there is an operation in a buffered runner
     */
    public function thereIsAnOperationInABufferedRunner()
    {
        $this->bufferedOperationRunner->run(new Callback(function () {
            $this->response = 'Buffered operation ran!';
        }));
    }

    /**
     * @When I send a request
     */
    public function iSendARequest()
    {
        $this->iSendARequestTo('/');
    }

    /**
     * @When I send a request to :path
     */
    public function iSendARequestTo($path)
    {
        $request = Request::create($path, 'GET');
        $this->response = $this->kernel->handle($request);
    }

    /**
     * @When the kernel terminates
     */
    public function theKernelTerminates()
    {
        $request = Request::create('/', 'GET');
        $response = $this->kernel->handle($request);

        $this->kernel->terminate($request, $response);
    }

    /**
     * @Then the buffered operation should have been run
     */
    public function theBufferedOperationShouldHaveBeenRun()
    {
        if ($this->response != 'Buffered operation ran!') {
            throw new \RuntimeException('The buffered operation seems to be not run');
        }
    }

    /**
     * @Then the buffered operation should not have been run
     */
    public function theBufferedOperationShouldNotHaveBeenRun()
    {
        if ($this->response == 'Buffered operation ran!') {
            throw new \RuntimeException('The buffered operation seems to be not run');
        }
    }
}
