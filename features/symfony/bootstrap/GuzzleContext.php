<?php

use Behat\Behat\Context\Context;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\RejectedPromise;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Tolerance\Bridge\Guzzle\HookableMiddlewareFactory;
use Tolerance\Bridge\Symfony\Bundle\AppBundle\ThirdParty\ApiClient;
use Tolerance\Bridge\Symfony\Bundle\AppBundle\ThirdParty\ApiException;

class GuzzleContext implements Context
{
    /**
     * @var ApiClient
     */
    private $apiClient;

    /**
     * @var HookableMiddlewareFactory
     */
    private $hookableMiddlewareFactory;

    /**
     * @var RequestInterface|null
     */
    private $request;

    /**
     * @param HookableMiddlewareFactory $hookableMiddlewareFactory
     * @param ApiClient $apiClient
     */
    public function __construct(HookableMiddlewareFactory $hookableMiddlewareFactory, ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
        $this->hookableMiddlewareFactory = $hookableMiddlewareFactory;
    }

    /**
     * @Given the 3rd party API will succeed
     */
    public function theRdPartyApiWillSucceed()
    {
        $this->hookableMiddlewareFactory->setHook(function(RequestInterface $request) {
            $this->request = $request;

            return new FulfilledPromise(new Response(200, [], 'Successful!'));
        });
    }

    /**
     * @Given the 3rd party API will fail
     */
    public function theRdPartyApiWillFail()
    {
        $this->hookableMiddlewareFactory->setHook(function(RequestInterface $request) {
            $this->request = $request;

            return new RejectedPromise(new ClientException('Blah blah', $request));
        });
    }

    /**
     * @When I send a request to the 3rd party API
     */
    public function iSendARequestToTheRdPartyApi()
    {
        try {
            $this->apiClient->get();
        } catch (ApiException $e) {}
    }

    /**
     * @Then the sent request should contain an :header header
     */
    public function theSentRequestShouldContainAnHeader($header)
    {
        if (null === $this->request) {
            throw new \RuntimeException('No request found, maybe no registered hook, right?');
        } else if (!$this->request->hasHeader($header)) {
            throw new \RuntimeException('Header not found in request');
        }
    }
}
