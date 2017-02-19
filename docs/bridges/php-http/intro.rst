php-http
===============

The Tolerance library comes with a `php-http <http://docs.php-http.org/en/latest/>`_ plugin to support retry operation through Tolerance:

.. code-block:: php

    use Http\Client\Common\PluginClient;
    use Tolerance\Bridge\PhpHttp\RetryPlugin;
    use Tolerance\Waiter\SleepWaiter;
    use Tolerance\Waiter\CountLimited;

    $this->httpClient = new PluginClient(
        $this->delegateHttpClient,
        [
            new RetryPlugin(new CountLimited(new SleepWaiter(), 3)), // will retry 3 times with 1 second tempo
        ]
    );

By default, Tolerance will retry every request that lead to response status code >= 500. If you want to customize the http status code list you should use 

.. code-block:: php

    use Http\Client\Common\PluginClient;
    use Tolerance\Bridge\PhpHttp\RetryPlugin;
    use Tolerance\Bridge\PhpHttp\StatusCodeVoter;
    use Tolerance\Waiter\SleepWaiter;
    use Tolerance\Waiter\CountLimited;

    $this->httpClient = new PluginClient(
        $this->delegateHttpClient,
        [
            new RetryPlugin(
                new CountLimited(new SleepWaiter(), 3),
                new StatusCodeVoter([502, 504]) // will only retry 502 and 504 response
            ),
        ]
    );
