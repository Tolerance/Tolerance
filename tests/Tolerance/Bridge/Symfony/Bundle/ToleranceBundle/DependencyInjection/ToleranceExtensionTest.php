<?php

namespace Tolerance\Bridge\Symfony\Bundle\ToleranceBundle\DependencyInjection;

use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Definition;

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

    public function test_that_public_request_identifier_services_are_successfully_created()
    {
        $definitionArgument = Argument::type('Symfony\Component\DependencyInjection\Definition');

        $builder = $this->createBuilder();
        $builder->addResource(Argument::type('Symfony\Component\Config\Resource\ResourceInterface'))->shouldBeCalled();
        $builder->setDefinition('tolerance.request_identifier.storage', $definitionArgument)->shouldBeCalled();
        $builder->setDefinition('tolerance.request_identifier.generator', $definitionArgument)->shouldBeCalled();
        $builder->setDefinition('tolerance.request_identifier.resolver', $definitionArgument)->shouldBeCalled();
        $builder->setParameter('tolerance.request_identifier.header', 'X-Request-Id')->shouldBeCalled();

        $this->extension->load([
            'tolerance' => [
                'request_identifier' => null,
            ]
        ], $builder->reveal());
    }

    public function test_that_request_identifier_listener_is_created()
    {
        $definitionArgument = Argument::type('Symfony\Component\DependencyInjection\Definition');

        $builder = $this->createBuilder();
        $builder->addResource(Argument::type('Symfony\Component\Config\Resource\ResourceInterface'))->shouldBeCalled();
        $builder->setDefinition(Argument::any(), $definitionArgument)->willReturn(null);
        $builder->setParameter('tolerance.request_identifier.header', 'X-Request-Id')->shouldBeCalled();
        $builder->setDefinition('tolerance.request_identifier.headers_listener', Argument::that(function(Definition $definition) {
            return $definition->hasTag('kernel.event_listener');
        }));

        $this->extension->load([
            'tolerance' => [
                'request_identifier' => null,
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
        $builder->setDefinition('tolerance.request_identifier.monolog.processor', Argument::that(function(Definition $definition) {
            return $definition->hasTag('kernel.event_listener');
        }));

        $this->extension->load([
            'tolerance' => [
                'request_identifier' => [
                    'monolog' => true,
                ],
            ]
        ], $builder->reveal());
    }

    public function test_it_adds_the_guzzle_middleware()
    {
        $definitionArgument = Argument::type('Symfony\Component\DependencyInjection\Definition');

        $builder = $this->createBuilder();
        $builder->addResource(Argument::type('Symfony\Component\Config\Resource\ResourceInterface'))->shouldBeCalled();
        $builder->setDefinition(Argument::any(), $definitionArgument)->willReturn(null);
        $builder->setParameter(Argument::any(), Argument::any())->shouldBeCalled();
        $builder->setDefinition('tolerance.request_identifier.guzzle_middleware', Argument::that(function(Definition $definition) {
            return $definition->hasTag('csa_guzzle.middleware');
        }));

        $this->extension->load([
            'tolerance' => [
                'request_identifier' => null,
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

    private function createBuilder()
    {
        $builder = $this->prophesize('Symfony\Component\DependencyInjection\ContainerBuilder');
        $builder->hasExtension('http://symfony.com/schema/dic/services')->willReturn(false);
        $builder->setDefinition(Argument::any(), Argument::type('Symfony\Component\DependencyInjection\Definition'))->willReturn(null);
        $builder->setParameter('tolerance.aop.enabled', Argument::any())->shouldBeCalled();

        return $builder;
    }
}
