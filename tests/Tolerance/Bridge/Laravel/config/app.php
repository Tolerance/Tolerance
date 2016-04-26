<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'UTC',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'log' => env('APP_LOG', 'single'),
    'providers' => [
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        \Illuminate\Foundation\Providers\ArtisanServiceProvider::class,
        \Fidry\LaravelYaml\Functional\App\ExtensionProvider::class,
        \Fidry\LaravelYaml\Functional\App\RegularServiceProvider::class,
//        \Fidry\LaravelYaml\Functional\App\AppProvider::class,
    ],
    'aliases' => [],
];
