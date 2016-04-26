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

use Fidry\LaravelYaml\Provider\AbstractExtensionProvider;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class ExtensionProvider extends AbstractExtensionProvider
{
    /**
     * {@inheritdoc}
     */
    public function getExtensions()
    {
        return [
            AppExtension::class,
        ];
    }
}
