<?php

namespace Tests\JobBoy\Bundle\JobBoyBundle\Flow\Domain\FlowManager\TransitionLoader;

use JobBoy\Bundle\JobBoyBundle\Flow\Domain\FlowManager\TransitionLoader\RegisterTransitionSetProvidersTransitionLoaderDecorator;
use JobBoy\Flow\Domain\FlowManager\DefaultTransitionRegistry;
use JobBoy\Flow\Domain\FlowManager\TransitionLoader\DefaultTransitionLoader;
use JobBoy\Flow\Domain\FlowManager\TransitionLoader\TransitionLoader;
use PHPUnit\Framework\TestCase;

class RegisterTransitionSetProvidersTransitionLoaderDecoratorTest extends TestCase
{

    /**
     * @test
     */
    public function it_MUST_be_a_decorator()
    {
        $transitionRegistry = new DefaultTransitionRegistry();
        $transitionLoader = new DefaultTransitionLoader($transitionRegistry);
        $this->assertInstanceOf(
            TransitionLoader::class,
            new RegisterTransitionSetProvidersTransitionLoaderDecorator($transitionLoader),
            'You need a decorator (not only an adapter) because of the DIC service loading strategy'
        );
    }
}
