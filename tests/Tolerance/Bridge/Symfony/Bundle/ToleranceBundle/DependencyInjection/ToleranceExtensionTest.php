<?php

namespace Tolerance\Bridge\Symfony\Bundle\ToleranceBundle\DependencyInjection;

use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

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
        $builder->setDefinition(Argument::any(), $definitionArgument)->willReturn(null);
        $builder->setParameter(Argument::any(), Argument::any())->shouldBeCalled();
        $builder->setDefinition('tolerance.operation_runner_listeners.buffered_termination', Argument::that(function(Definition $definition) {
            return $definition->hasTag('kernel.event_subscriber');
        }))->shouldBeCalled();

        $this->extension->load([], $builder->reveal());
    }

    public function test_it_do_not_register_the_buffered_runner_termination_listener()
    {
        $definitionArgument = Argument::type('Symfony\Component\DependencyInjection\Definition');

        $builder = $this->createBuilder();
        $builder->setDefinition(Argument::any(), $definitionArgument)->willReturn(null);
        $builder->setParameter(Argument::any(), Argument::any())->shouldBeCalled();
        $builder->setDefinition('tolerance.operation_runner_listeners.buffered_termination', Argument::any())->shouldNotBeCalled();

        $this->extension->load([
            'tolerance' => [
                'operation_runner_listener' => false,
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

    public function test_it_registers_the_timeout_waiter_service()
    {
        $definitionArgument = Argument::type('Symfony\Component\DependencyInjection\Definition');

        $builder = $this->createBuilder();
        $builder->setDefinition(Argument::any(), $definitionArgument)->willReturn(null);
        $builder->setDefinition('tolerance.operation_runners.foo.waiter', Argument::any())->shouldBeCalled();

        $this->extension->load([
            'tolerance' => [
                'operation_runners' => [
                    'foo' => [
                        'retry' => [
                            'runner' => ['callback' => null],
                            'waiter' => [
                                'timeout' => [
                                    'waiter' => ['null' => null],
                                    'timeout' => 60,
                                ]
                            ],
                        ]
                    ]
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

        if (method_exists(ContainerBuilder::class, 'fileExists')) {
            $builder->fileExists(Argument::any())->will(function ($arguments) {
                return file_exists($arguments[0]);
            });
        }

        $definition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $definition->replaceArgument(Argument::any(), Argument::any())->willReturn($definition);

        $builder->getDefinition(Argument::type('string'))->willReturn($definition);

        return $builder;
    }
}
