<?php

namespace Tolerance\Bridge\Symfony\Bundle\AppBundle\ThirdParty;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;

class GuzzleBasedApiClient implements ApiClient
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        try {
            $response = $this->client->request('GET', '/');
        } catch (TransferException $e) {
            throw new ApiException($e->getMessage(), 0, $e);
        }

        return $response->getBody()->getContents();
    }
}
