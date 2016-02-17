<?php

namespace Tolerance\Bridge\Symfony\Bundle\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class DefaultController
{
    public function indexAction()
    {
        return new Response('OK');
    }

    public function willThrowAnExceptionAction()
    {
        throw new \RuntimeException('My exception');
    }
}
