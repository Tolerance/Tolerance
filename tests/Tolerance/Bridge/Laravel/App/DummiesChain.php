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

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class DummiesChain
{
    private $dummies = [];

    public function __construct(array $dummies)
    {
        $this->dummies = $dummies;
    }

    public function getDummies()
    {
        return $this->dummies;
    }
}
