<?php

namespace Tests\JobBoy\Bundle\JobBoyBundle\Flow\Domain\FlowManager;

use JobBoy\Bundle\JobBoyBundle\Flow\Domain\FlowManager\RegisterHasNodeTransitionsTransitionRegistryDecorator;
use JobBoy\Flow\Domain\FlowManager\DefaultTransitionRegistry;
use JobBoy\Flow\Domain\FlowManager\TransitionRegistry;
use PHPUnit\Framework\TestCase;

class RegisterHasNodeTransitionsTransitionRegistryDecoratorTest extends TestCase
{

    /**
     * @test
     */
    public function it_MUST_be_a_decorator()
    {
        $transitionRegistry = new DefaultTransitionRegistry();
        $this->assertInstanceOf(
            TransitionRegistry::class,
            new RegisterHasNodeTransitionsTransitionRegistryDecorator($transitionRegistry),
            'You need a decorator (not only an adapter) because of the DIC service loading strategy'
        );
    }

}
