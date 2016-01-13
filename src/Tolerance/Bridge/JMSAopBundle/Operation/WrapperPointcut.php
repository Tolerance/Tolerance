<?php

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
