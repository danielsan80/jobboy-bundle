<?php
declare(strict_types=1);

namespace JobBoy\Bundle\JobBoyBundle\Flow\Domain\FlowManager\TransitionLoader;

use JobBoy\Flow\Domain\FlowManager\TransitionLoader\TransitionLoader;
use JobBoy\Flow\Domain\FlowManager\TransitionLoader\TransitionSet;
use JobBoy\Flow\Domain\FlowManager\TransitionLoader\TransitionSetProvider;

/**
 * You need a decorator (not only an adapter) because of the DIC service loading strategy
 */
class RegisterTransitionSetProvidersTransitionLoaderDecorator implements TransitionLoader
{

    private $transitionLoader;

    public function __construct(TransitionLoader $transitionLoader)
    {
        $this->transitionLoader = $transitionLoader;
    }

    public function loadProvider(TransitionSetProvider $transitionSetProvider): void
    {
        $this->transitionLoader->load($transitionSetProvider->get());
    }

    public function load(TransitionSet $transitionSet): void
    {
        $this->transitionLoader->load($transitionSet);
    }
}
