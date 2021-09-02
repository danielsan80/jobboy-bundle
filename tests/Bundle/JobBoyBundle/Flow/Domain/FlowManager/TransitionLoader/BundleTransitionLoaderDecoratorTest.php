<?php

namespace Tests\Bundle\JobBoyBundle\Flow\Domain\FlowManager\TransitionLoader;

use JobBoy\Bundle\JobBoyBundle\Flow\Domain\FlowManager\TransitionLoader\BundleTransitionLoaderDecorator;
use JobBoy\Flow\Domain\FlowManager\TransitionLoader\TransitionLoader;
use JobBoy\Flow\Domain\FlowManager\TransitionRegistry;
use PHPUnit\Framework\TestCase;

class BundleTransitionLoaderDecoratorTest extends TestCase
{

    /**
     * @test
     */
    public function it_MUST_be_a_decorator()
    {
        $transitionRegistry = new TransitionRegistry();
        $transitionLoader = new TransitionLoader($transitionRegistry);
        $this->assertInstanceOf(
            TransitionLoader::class,
            new BundleTransitionLoaderDecorator($transitionLoader),
            'You need a decorator (not only an adapter) because of the DIC service loading strategy'
        );
    }
}
