<?php

namespace Tolerance\Bridge\Laravel\Provider\MessageProfile;

use Tolerance\Bridge\Laravel\Illuminate\Support\ServiceProvider;

final class StorageProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->app->bind(
            'tolerance.message_profile.storage.profile_normalizer.simple',
            \Tolerance\MessageProfile\Storage\Normalizer\SimpleProfileNormalizer::class
        );

        $this->app->bind(
            'tolerance.message_profile.storage.in_memory',
            \Tolerance\MessageProfile\Storage\InMemoryStorage::class
        );
    }
}
