<?php

namespace spec\Tolerance\Bridge\JMSAopBundle\Operation;

use JMS\AopBundle\Aop\PointcutInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Tolerance\Bridge\JMSAopBundle\Operation\RunnerRepository;
use Tolerance\Operation\Runner\OperationRunner;

class WrapperPointcutSpec extends ObjectBehavior
{
    function let(RunnerRepository $runnerRepository)
    {
        $this->beConstructedWith($runnerRepository);
    }

    function it_is_a_pointcut()
    {
        $this->shouldImplement(PointcutInterface::class);
    }

    function it_matches_the_classes_that_have_a_runner(RunnerRepository $runnerRepository, \ReflectionClass $class)
    {
        $runnerRepository->hasRunnerForClass($class)->willReturn(true);

        $this->matchesClass($class)->shouldBe(true);
    }

    function it_do_not_match_the_classes_that_do_not_have_a_runner(RunnerRepository $runnerRepository, \ReflectionClass $class)
    {
        $runnerRepository->hasRunnerForClass($class)->willReturn(false);

        $this->matchesClass($class)->shouldBe(false);
    }

    function it_matches_the_methods_that_have_a_runner(RunnerRepository $runnerRepository, \ReflectionMethod $method, OperationRunner $runner)
    {
        $runnerRepository->getRunnerByMethod($method)->willReturn($runner);

        $this->matchesMethod($method)->shouldBe(true);
    }

    function it_do_not_matches_the_methods_that_do_not_have_a_runner(RunnerRepository $runnerRepository, \ReflectionMethod $method)
    {
        $runnerRepository->getRunnerByMethod($method)->willReturn(null);

        $this->matchesMethod($method)->shouldBe(false);
    }
}
