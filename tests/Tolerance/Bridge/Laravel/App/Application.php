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

use Illuminate\Foundation\Application as IlluminateFoundationApplication;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class Application extends IlluminateFoundationApplication
{
    /**
     * {@inheritdoc}
     */
    public function __construct($basePath)
    {
        parent::__construct($basePath);

        $this->useStoragePath($this->basePath.DIRECTORY_SEPARATOR.'var');
    }

    /**
     * {@inheritdoc}
     */
    public function configPath()
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'config';
    }

    /**
     * {@inheritdoc}
     */
    public function getCachedRoutesPath()
    {
        return $this->cachePath().'/routes.php';
    }

    /**
     * {@inheritdoc}
     */
    public function getCachedCompilePath()
    {
        return $this->cachePath().'/compiled.php';
    }

    /**
     * {@inheritdoc}
     */
    public function getCachedServicesPath()
    {
        return $this->cachePath().'/services.php';
    }

    public function langPath()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function path()
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'src';
    }

    /**
     * @return string
     */
    private function cachePath()
    {
        return $this->basePath().'/cache';
    }
}
