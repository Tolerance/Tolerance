<?php

namespace Tolerance\Bridge\Symfony\Bundle\ToleranceBundle\DependencyInjection;

use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Tolerance\Bridge\RabbitMqBundle\MessageProfile\StoreMessageProfileConsumer;
use Tolerance\Bridge\RabbitMqBundle\MessageProfile\StoreMessageProfileProducer;

class ToleranceExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ToleranceExtension
     */
    private $extension;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->extension = new ToleranceExtension();
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        unset($this->extension);
    }

    public function test_the_correct_extension_interface()
    {
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Extension\ExtensionInterface', $this->extension);
    }

    public function test_that_it_creates_the_kernel_listener_to_store_the_request_profile()
    {
        $definitionArgument = Argument::type('Symfony\Component\DependencyInjection\Definition');

        $builder = $this->createBuilder();
        $builder->addResource(Argument::type('Symfony\Component\Config\Resource\ResourceInterface'))->shouldBeCalled();
        $builder->setDefinition(Argument::any(), $definitionArgument)->willReturn(null);
        $builder->setParameter('tolerance.message_profile.header', 'x-message-id')->shouldBeCalled();
        $builder->setDefinition('tolerance.message_profile.stores_profile', Argument::that(function(Definition $definition) {
            return $definition->hasTag('kernel.event_listener');
        }));

        $this->extension->load([
            'tolerance' => [
                'message_profile' => [
                    'storage' => [
                        'in_memory' => null
                    ],
                ],
            ]
        ], $builder->reveal());
    }

    public function test_that_it_adds_the_monolog_processor()
    {
        $definitionArgument = Argument::type('Symfony\Component\DependencyInjection\Definition');

        $builder = $this->createBuilder();
        $builder->addResource(Argument::type('Symfony\Component\Config\Resource\ResourceInterface'))->shouldBeCalled();
        $builder->setDefinition(Argument::any(), $definitionArgument)->willReturn(null);
        $builder->setParameter(Argument::any(), Argument::any())->shouldBeCalled();
        $builder->setDefinition('tolerance.message_profile.monolog.request_identifier_processor', Argument::that(function(Definition $definition) {
            return $definition->hasTag('kernel.event_listener');
        }));

        $this->extension->load([
            'tolerance' => [
                'message_profile' => [
                    'integrations' => [
                        'monolog' => true,
                    ],
                    'storage' => [
                        'in_memory' => null
                    ],
                ],
            ]
        ], $builder->reveal());
    }

    public function test_it_adds_the_guzzle_middlewares()
    {
        $definitionArgument = Argument::type('Symfony\Component\DependencyInjection\Definition');

        $builder = $this->createBuilder();
        $builder->addResource(Argument::type('Symfony\Component\Config\Resource\ResourceInterface'))->shouldBeCalled();
        $builder->setDefinition(Argument::any(), $definitionArgument)->willReturn(null);
        $builder->setParameter(Argument::any(), Argument::any())->shouldBeCalled();
        $builder->setDefinition('tolerance.message_profile.guzzle.middleware.store_profile', Argument::that(function(Definition $definition) {
            return $definition->hasTag('csa_guzzle.middleware');
        }));
        $builder->setDefinition('tolerance.message_profile.guzzle.middleware.message_identifier', Argument::that(function(Definition $definition) {
            return $definition->hasTag('csa_guzzle.middleware');
        }));

        $this->extension->load([
            'tolerance' => [
                'message_profile' => [
                    'storage' => [
                        'in_memory' => null
                    ],
                ],
            ]
        ], $builder->reveal());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function test_it_throws_an_exception_if_aop_without_jms_bundle()
    {
        $definitionArgument = Argument::type('Symfony\Component\DependencyInjection\Definition');

        $builder = $this->createBuilder();
        $builder->setDefinition(Argument::any(), $definitionArgument)->willReturn(null);
        $builder->setParameter(Argument::any(), Argument::any())->shouldBeCalled();

        $this->extension->load([
            'tolerance' => [
                'aop' => null,
            ]
        ], $builder->reveal());
    }

    public function test_it_registers_the_buffered_runner_termination_listener()
    {
        $definitionArgument = Argument::type('Symfony\Component\DependencyInjection\Definition');

        $builder = $this->createBuilder();
        $builder->addResource(Argument::type('Symfony\Component\Config\Resource\ResourceInterface'))->shouldBeCalled();
        $builder->setDefinition(Argument::any(), $definitionArgument)->willReturn(null);
        $builder->setParameter(Argument::any(), Argument::any())->shouldBeCalled();
        $builder->setDefinition('tolerance.operation_runner_listeners.buffered_termination', Argument::that(function(Definition $definition) {
            return $definition->hasTag('kernel.event_listener');
        }))->shouldBeCalled();

        $this->extension->load([], $builder->reveal());
    }

    public function test_it_do_not_register_the_buffered_runner_termination_listener()
    {
        $definitionArgument = Argument::type('Symfony\Component\DependencyInjection\Definition');

        $builder = $this->createBuilder();
        $builder->addResource(Argument::type('Symfony\Component\Config\Resource\ResourceInterface'))->shouldBeCalled();
        $builder->setDefinition(Argument::any(), $definitionArgument)->willReturn(null);
        $builder->setParameter(Argument::any(), Argument::any())->shouldBeCalled();
        $builder->setDefinition('tolerance.operation_runner_listeners.buffered_termination', Argument::any())->shouldNotBeCalled();

        $this->extension->load([
            'tolerance' => [
                'operation_runner_listener' => false,
            ]
        ], $builder->reveal());
    }

    public function test_it_register_decorators_for_rabbitmq_producers()
    {
        $definitionArgument = Argument::type('Symfony\Component\DependencyInjection\Definition');

        $builder = $this->createBuilder();
        $builder->addResource(Argument::type('Symfony\Component\Config\Resource\ResourceInterface'))->shouldBeCalled();
        $builder->setDefinition(Argument::any(), $definitionArgument)->willReturn(null);
        $builder->setParameter(Argument::any(), Argument::any())->shouldBeCalled();

        $builder->findTaggedServiceIds('old_sound_rabbit_mq.producer')->shouldBeCalled()->willReturn([
            'service.id' => [[]],
        ]);

        $builder->setDefinition('service.id.tolerance_decorator', Argument::that(function(Definition $definition) {
            return $definition->getDecoratedService()[0] == 'service.id' &&
            $definition->getClass() == StoreMessageProfileProducer::class;
        }));

        $this->extension->load([
            'tolerance' => [
                'message_profile' => [
                    'integrations' => [
                        'rabbitmq' => true,
                    ],
                    'storage' => [
                        'in_memory' => null
                    ],
                ],
            ]
        ], $builder->reveal());
    }

    public function test_it_register_decorators_for_rabbitmq_consumers()
    {
        $definitionArgument = Argument::type('Symfony\Component\DependencyInjection\Definition');

        $builder = $this->createBuilder();
        $builder->addResource(Argument::type('Symfony\Component\Config\Resource\ResourceInterface'))->shouldBeCalled();
        $builder->setDefinition(Argument::any(), $definitionArgument)->willReturn(null);
        $builder->setParameter(Argument::any(), Argument::any())->shouldBeCalled();

        $builder->findTaggedServiceIds('old_sound_rabbit_mq.consumer')->shouldBeCalled()->willReturn([
            'service.id' => [[]],
        ]);

        $builder->setDefinition('service.id.tolerance_decorator', Argument::that(function(Definition $definition) {
            return $definition->getDecoratedService()[0] == 'service.id' &&
            $definition->getClass() == StoreMessageProfileConsumer::class;
        }));

        $this->extension->load([
            'tolerance' => [
                'message_profile' => [
                    'integrations' => [
                        'rabbitmq' => true,
                    ],
                    'storage' => [
                        'in_memory' => null
                    ],
                ],
            ]
        ], $builder->reveal());
    }

    public function test_it_configure_the_metrics_command()
    {
        $definitionArgument = $this->prophesize('Symfony\Component\DependencyInjection\Definition');

        $builder = $this->createBuilder();
        $builder->getDefinition('tolerance.metrics.command.collect_and_publish')->willReturn($definitionArgument);

        $definitionArgument->replaceArgument(0, new Reference('tolerance.metrics.collector.collection'))->shouldBeCalled()->willReturn($definitionArgument);
        $definitionArgument->replaceArgument(1, new Reference('my_publisher_service'))->shouldBeCalled()->willReturn($definitionArgument);

        $this->extension->load([
            'tolerance' => [
                'metrics' => [
                    'command' => [
                        'publisher' => 'my_publisher_service'
                    ]
                ],
            ]
        ], $builder->reveal());
    }

    public function test_it_do_not_add_the_request_listener_when_configured()
    {
        $builder = $this->createBuilder();
        $builder->setDefinition('tolerance.metrics.listener.request_ended.send_time_to_publishers', Argument::any())->shouldNotBeCalled();

        $this->extension->load([
            'tolerance' => [
                'metrics' => [],
            ]
        ], $builder->reveal());

    }

    public function test_it_adds_the_request_listener_when_configured()
    {
        $builder = $this->createBuilder();
        $builder->setDefinition('tolerance.metrics.listener.request_ended.send_time_to_publishers', Argument::any())->shouldBeCalled();

        $this->extension->load([
            'tolerance' => [
                'metrics' => [
                    'request' => null,
                ],
            ]
        ], $builder->reveal());
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private function createBuilder()
    {
        $builder = $this->prophesize('Symfony\Component\DependencyInjection\ContainerBuilder');
        $builder->hasExtension('http://symfony.com/schema/dic/services')->willReturn(false);
        $builder->setDefinition(Argument::any(), Argument::type('Symfony\Component\DependencyInjection\Definition'))->willReturn(null);
        $builder->setAlias(Argument::any(), Argument::any())->willReturn(null);
        $builder->setParameter(Argument::any(), Argument::any())->willReturn(null);
        $builder->findTaggedServiceIds(Argument::any())->willReturn([]);
        $builder->addResource(Argument::type('Symfony\Component\Config\Resource\ResourceInterface'))->willReturn(null);

        $definition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $definition->replaceArgument(Argument::any(), Argument::any())->willReturn($definition);

        $builder->getDefinition(Argument::type('string'))->willReturn($definition);

        return $builder;
    }
}
