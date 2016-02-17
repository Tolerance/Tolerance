<?php

use Behat\Behat\Context\Context;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Tolerance\Bridge\Symfony\Bundle\AppBundle\ThirdParty\ApiClient;
use Tolerance\Bridge\Symfony\Bundle\AppBundle\ThirdParty\ApiException;
use Tolerance\Bridge\Symfony\Bundle\AppBundle\ThirdParty\StepByStepHookApiClient;
use Tolerance\Operation\Callback;
use Tolerance\Operation\Runner\BufferedOperationRunner;

class FeatureContext implements Context
{
    /**
     * @var StepByStepHookApiClient
     */
    private $stepByStepHookApiClient;

    /**
     * @var ApiClient
     */
    private $client;

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
     * @param Kernel $kernel
     * @param ApiClient $client
     * @param BufferedOperationRunner $bufferedOperationRunner
     */
    public function __construct(Kernel $kernel, ApiClient $client, BufferedOperationRunner $bufferedOperationRunner)
    {
        $this->stepByStepHookApiClient = $client;
        $this->client = $client;
        $this->kernel = $kernel;
        $this->bufferedOperationRunner = $bufferedOperationRunner;
    }

    /**
     * @Given the 3rd party API will fail at the 1st run
     */
    public function theRdPartyApiWillFailAtTheStRun()
    {
        $this->stepByStepHookApiClient->registerStepHook(0, function () {
            throw new ApiException('That first step that fails');
        });
    }

    /**
     * @Given the 3rd party API will succeed at the 2nd run
     */
    public function theRdPartyApiWillSucceedAtTheNdRun()
    {
        $this->stepByStepHookApiClient->registerStepHook(1, function () {
            return 'The good API answer';
        });
    }

    /**
     * @Given there is an operation in a buffered runner
     */
    public function thereIsAnOperationInABufferedRunner()
    {
        $this->bufferedOperationRunner->run(new Callback(function() {
            $this->response = 'Buffered operation ran!';
        }));
    }

    /**
     * @When I call my local client service
     */
    public function iCallMyLocalClientService()
    {
        $this->response = $this->client->get();
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

    /**
     * @Then I should see the call as successful
     */
    public function iShouldSeeTheCallAsSuccessful()
    {
        if ($this->response != 'The good API answer') {
            throw new \RuntimeException('The found answer is not matching the expected one');
        }
    }
}
