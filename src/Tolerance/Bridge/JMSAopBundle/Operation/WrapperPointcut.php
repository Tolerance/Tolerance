<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\JMSAopBundle\Operation;

use JMS\AopBundle\Aop\PointcutInterface;

class WrapperPointcut implements PointcutInterface
{
    /**
     * @var RunnerRepository
     */
    private $runnerRepository;

    /**
     * @param RunnerRepository $runnerRepository
     */
    public function __construct(RunnerRepository $runnerRepository)
    {
        $this->runnerRepository = $runnerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function matchesClass(\ReflectionClass $class)
    {
        return $this->runnerRepository->hasRunnerForClass($class);
    }

    /**
     * {@inheritdoc}
     */
    public function matchesMethod(\ReflectionMethod $method)
    {
        return $this->runnerRepository->getRunnerByMethod($method) !== null;
    }
}
