<?php

namespace Tolerance\Bridge\Laravel\Provider\MessageProfile;

use Tolerance\Bridge\Laravel\Illuminate\Support\ServiceProvider;

final class MonologProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerService(
            'tolerance.message_profile.monolog.request_identifier_processor',
            \Tolerance\Bridge\Monolog\MessageProfile\RequestIdentifierProcessor::class,
            function ($app) {
                $resolver = $app->make('tolerance.message_profile.http_foundation.header_request_identifier_resolver');

                $request = $app->make(\Illuminate\Http\Request::class);

                return new \Tolerance\Bridge\Monolog\MessageProfile\RequestIdentifierProcessor(
                    $resolver,
                    $request
                );
            },
            ['monolog.processor']
        );
    }
}
