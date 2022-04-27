<?php

namespace Tests\JobBoy\Bundle\JobBoyBundle\Flow\Domain\FlowManager\TransitionLoader;

use JobBoy\Bundle\JobBoyBundle\Flow\Domain\FlowManager\TransitionLoader\WithTransitionLoaderTransitionRegistryDecorator;
use JobBoy\Flow\Domain\FlowManager\DefaultTransitionRegistry;
use JobBoy\Flow\Domain\FlowManager\TransitionLoader\DefaultTransitionLoader;
use JobBoy\Flow\Domain\FlowManager\TransitionRegistry;
use PHPUnit\Framework\TestCase;

class WithTransitionLoaderTransitionRegistryDecoratorTest extends TestCase
{
    /**
     * @test
     */
    public function it_MUST_be_a_decorator()
    {
        $transitionRegistry = new DefaultTransitionRegistry();
        $transitionLoader = new DefaultTransitionLoader($transitionRegistry);
        $this->assertInstanceOf(
            TransitionRegistry::class,
            new WithTransitionLoaderTransitionRegistryDecorator($transitionRegistry, $transitionLoader),
            'You need a decorator (not only an adapter) because of the DIC service loading strategy'
        );
    }
}
