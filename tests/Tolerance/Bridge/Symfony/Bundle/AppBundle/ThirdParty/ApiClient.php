<?php

namespace Tolerance\Bridge\Symfony\Bundle\AppBundle\ThirdParty;

interface ApiClient
{
    /**
     * Get something from the API client.
     *
     * This is used for demo and testing purposes only, obviously :)
     *
     * @throws ApiException
     *
     * @return string
     */
    public function get();
}
