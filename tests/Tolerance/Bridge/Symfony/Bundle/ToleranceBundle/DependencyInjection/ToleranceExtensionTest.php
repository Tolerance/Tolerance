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
        $builder->setDefinition(Argument::any(), $definitionArgument)->willReturn(null);
        $builder->setDefinition('tolerance.request_identifier.storage', $definitionArgument)->shouldBeCalled();
        $builder->setDefinition('tolerance.request_identifier.generator', $definitionArgument)->shouldBeCalled();
        $builder->setDefinition('tolerance.request_identifier.resolver', $definitionArgument)->shouldBeCalled();

        $this->extension->load([], $builder->reveal());
    }

    public function test_that_request_identifier_listener_is_created()
    {
        $definitionArgument = Argument::type('Symfony\Component\DependencyInjection\Definition');

        $builder = $this->createBuilder();
        $builder->setDefinition(Argument::any(), $definitionArgument)->willReturn(null);
        $builder->setDefinition('tolerance.request_identifier.headers_listener', Argument::that(function(Definition $definition) {
            return $definition->hasTag('kernel.event_listener');
        }));
    }

    private function createBuilder()
    {
        $builder = $this->prophesize('Symfony\Component\DependencyInjection\ContainerBuilder');
        $builder->hasExtension('http://symfony.com/schema/dic/services')->willReturn(false);

        return $builder;
    }
}
