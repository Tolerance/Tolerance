<?php

namespace Tolerance\Bridge\Symfony\Bundle\ToleranceBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Tolerance\Bridge\RabbitMqBundle\Tracer\TracedConsumer;
use Tolerance\Bridge\RabbitMqBundle\Tracer\TracedProducer;

class RabbitMqCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('tolerance.tracer.rabbitmq.enabled')) {
            return;
        }

        foreach ($container->findTaggedServiceIds('old_sound_rabbit_mq.producer') as $id => $attributes) {
            $this->decoratesRabbitMqTracerProducer($container, $id);
        }

        foreach ($container->findTaggedServiceIds('old_sound_rabbit_mq.consumer') as $id => $attributes) {
            $this->decoratesRabbitMqTracerConsumer($container, $id);
        }

        foreach ($container->getParameter('tolerance.tracer.rabbitmq.consumers') as $id) {
            $this->decoratesRabbitMqTracerConsumer($container, $id);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string $serviceId
     */
    private function decoratesRabbitMqTracerProducer(ContainerBuilder $container, $serviceId)
    {
        $decoratorId = $serviceId . '.tolerance_decorator';
        $decoratorDefinition = new Definition(TracedProducer::class, [
            new Reference($decoratorId . '.inner'),
            new Reference('tolerance.tracer.span_factory.amqp'),
            new Reference('tolerance.tracer'),
        ]);

        $decoratorDefinition->setDecoratedService($serviceId);
        $container->setDefinition($decoratorId, $decoratorDefinition);
    }

    /**
     * @param ContainerBuilder $container
     * @param string $id
     */
    private function decoratesRabbitMqTracerConsumer(ContainerBuilder $container, $id)
    {
        $decoratorId = $id . '.tolerance_decorator';
        $decoratorDefinition = new Definition(TracedConsumer::class, [
            new Reference($decoratorId . '.inner'),
            new Reference('tolerance.tracer'),
            new Reference('tolerance.tracer.stack_stack'),
            new Reference('tolerance.tracer.span_factory.amqp'),
        ]);

        $decoratorDefinition->setDecoratedService($id);
        $container->setDefinition($decoratorId, $decoratorDefinition);
    }
}
