<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Bridge\Symfony\Bundle\ToleranceBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AopCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->getParameter('tolerance.aop.enabled')) {
            return;
        }

        $taggedServices = $container->findTaggedServiceIds('tolerance.operation_wrapper');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $tag) {
                $this->processOperationWrapperTag($container, $id, $tag);
            }
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $serviceId
     * @param array            $tag
     */
    private function processOperationWrapperTag(ContainerBuilder $container, $serviceId, array $tag)
    {
        $runnerRepositoryDefinition = $container->getDefinition('tolerance.aop.runner_repository');
        $serviceDefinition = $container->getDefinition($serviceId);

        foreach ($this->getMethodsFromTag($tag) as $method) {
            $runnerRepositoryDefinition->addMethodCall('addRunnerAt', [
                sprintf('%s:%s', $serviceDefinition->getClass(), $method),
                $this->getRunnerReferenceFromTag($tag),
            ]);
        }
    }

    /**
     * Get the list of methods defined by the tag.
     *
     * @param array $tag
     *
     * @return array
     */
    private function getMethodsFromTag(array $tag)
    {
        if (!array_key_exists('methods', $tag)) {
            throw new \RuntimeException('No "methods" attribute on tag');
        }

        return explode(',', $tag['methods']);
    }

    /**
     * Get the runner reference from the given tag.
     *
     * @param array $tag
     *
     * @return Reference
     */
    private function getRunnerReferenceFromTag(array $tag)
    {
        if (!array_key_exists('runner', $tag)) {
            throw new \RuntimeException('No "runner" attribute found on tag');
        }

        return new Reference($tag['runner']);
    }
}
