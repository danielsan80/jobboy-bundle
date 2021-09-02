<?php

namespace Tests\Bundle\JobBoyBundle\Flow\Domain\FlowManager;

use JobBoy\Bundle\JobBoyBundle\Flow\Domain\FlowManager\BundleTransitionRegistryDecorator;
use JobBoy\Flow\Domain\FlowManager\TransitionRegistry;
use PHPUnit\Framework\TestCase;

class BundleTransitionRegistryDecoratorTest extends TestCase
{

    /**
     * @test
     */
    public function it_MUST_be_a_decorator()
    {
        $transitionRegistry = new TransitionRegistry();
        $this->assertInstanceOf(
            TransitionRegistry::class,
            new BundleTransitionRegistryDecorator($transitionRegistry),
            'You need a decorator (not only an adapter) because of the DIC service loading strategy'
        );
    }

}
