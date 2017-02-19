<?php

use Behat\Behat\Context\Context;
use Tolerance\Bridge\Symfony\Bundle\AppBundle\ThirdParty\ApiClient;
use Tolerance\Bridge\Symfony\Bundle\AppBundle\ThirdParty\ApiException;
use Tolerance\Bridge\Symfony\Bundle\AppBundle\ThirdParty\StepByStepHookApiClient;

class AopContext implements Context
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
     * @var mixed
     */
    private $response;

    /**
     * @param ApiClient $client
     */
    public function __construct(ApiClient $client)
    {
        $this->stepByStepHookApiClient = $client;
        $this->client = $client;
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
     * @When I call my local client service
     */
    public function iCallMyLocalClientService()
    {
        $this->response = $this->client->get();
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
