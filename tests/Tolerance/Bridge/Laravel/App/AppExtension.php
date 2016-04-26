<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\Functional\App;

use Fidry\LaravelYaml\DependencyInjection\Builder\ContainerBuilder;
use Fidry\LaravelYaml\DependencyInjection\Extension\ExtensionInterface;
use Fidry\LaravelYaml\FileLoader\FileLoaderInterface;
use Fidry\LaravelYaml\FileLoader\Yaml\YamlFileLoader;
use Illuminate\Support\Facades\App;
use Symfony\Component\Config\FileLocator;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class AppExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     *
     * @return YamlFileLoader
     */
    public function load(ContainerBuilder $container)
    {
        $resourcePath = (function_exists('resource_path'))
            ? resource_path('providers')
            : __DIR__.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'providers'
        ;

        $rootDir = new FileLocator($resourcePath);
        $loader = new YamlFileLoader($container, $rootDir);

        $this
            ->loadResourceIfExist($loader, 'parameters.yml')
            ->loadResourceIfExist($loader, 'services.yml')
            ->loadResourceIfExist($loader, sprintf('parameters_%s.yml', App::environment()))
        ;

        return $loader;
    }

    /**
     * @param FileLoaderInterface $loader
     * @param string              $resource
     *
     * @return $this
     */
    protected function loadResourceIfExist(FileLoaderInterface $loader, $resource)
    {
        try {
            $loader->load($resource);
        } catch (\InvalidArgumentException $exception) {
            throw $exception;
            // Ignore error as is an optional file
        }

        return $this;
    }
}
