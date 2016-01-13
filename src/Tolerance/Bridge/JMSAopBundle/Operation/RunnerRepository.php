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

use Tolerance\Operation\Runner\OperationRunner;

class RunnerRepository
{
    /**
     * This array contains the mapping between the method and the runner.
     *
     * @var array
     */
    private $runnerMappings = [];

    /**
     * @param \ReflectionMethod $method
     *
     * @return OperationRunner|null
     */
    public function getRunnerByMethod(\ReflectionMethod $method)
    {
        $class = new \ReflectionClass($method->class);
        $className = $class->name;

        if (!array_key_exists($className, $this->runnerMappings)) {
            return;
        }

        $methodName = $method->name;
        if (!array_key_exists($methodName, $this->runnerMappings[$className])) {
            return;
        }

        return $this->runnerMappings[$className][$methodName];
    }

    /**
     * @param \ReflectionClass $class
     *
     * @return bool
     */
    public function hasRunnerForClass(\ReflectionClass $class)
    {
        return array_key_exists($class->name, $this->runnerMappings);
    }

    /**
     * Add the given runner for the given method.
     *
     * @param \ReflectionMethod $method
     * @param OperationRunner   $runner
     */
    public function addRunnerForMethod(\ReflectionMethod $method, OperationRunner $runner)
    {
        $class = new \ReflectionClass($method->class);

        $this->addRunner(
            $class->name,
            $method->name,
            $runner
        );
    }

    /**
     * @param string          $path
     * @param OperationRunner $runner
     */
    public function addRunnerAt($path, OperationRunner $runner)
    {
        if (strpos($path, ':') === false) {
            throw new \RuntimeException('The path should be with the form "class:method".');
        }

        list($className, $method) = explode(':', $path);

        $this->addRunner($className, $method, $runner);
    }

    /**
     * @param string          $className
     * @param string          $method
     * @param OperationRunner $runner
     */
    private function addRunner($className, $method, OperationRunner $runner)
    {
        if (!array_key_exists($className, $this->runnerMappings)) {
            $this->runnerMappings[$className] = [];
        }

        $this->runnerMappings[$className][$method] = $runner;
    }
}
