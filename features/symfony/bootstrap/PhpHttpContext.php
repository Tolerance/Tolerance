<?php

use Behat\Behat\Context\Context;
use Http\Mock\Client as MockClient;
use Http\Client\Common\PluginClient;
use Tolerance\Bridge\PhpHttp\RetryPlugin;
use Tolerance\Waiter\SleepWaiter;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use function GuzzleHttp\Psr7\copy_to_string;

class PhpHttpContext implements Context
{
    private $httpClient;

    private $mockClient;

    private $response;

    public function __construct()
    {
        $this->mockClient = new MockClient();
        $this->httpClient = new PluginClient(
            $this->mockClient,
            [
                new RetryPlugin(new SleepWaiter()),
                new \Http\Client\Common\Plugin\ErrorPlugin(),
            ]
        );
        $this->messageFactory = new GuzzleMessageFactory();
    }

    /**
     * @Given the 3rd party API will fail at the 1st run
     */
    public function theRdPartyApiWillFailAtTheStRun()
    {
        $this->mockClient->addResponse($this->messageFactory->createResponse(500, 'Internal Error'));
    }

    /**
     * @Given the 3rd party API will succeed at the 2nd run
     */
    public function theRdPartyApiWillSucceedAtTheNdRun()
    {
        $this->mockClient->addResponse($this->messageFactory->createResponse(200, 'ok', [], 'bingo !'));
    }

    /**
     * @When I call my local client service
     */
    public function iCallMyLocalClientService()
    {
        $this->response = $this->httpClient->sendRequest($this->messageFactory->createRequest('GET', '/'));
    }

    /**
     * @Then I should see the call as successful
     */
    public function iShouldSeeTheCallAsSuccessful()
    {
        if (copy_to_string($this->response->getBody()) != 'bingo !') {
            throw new \RuntimeException('The found answer is not matching the expected one');
        }
    }
}
