<?php
declare(strict_types=1);

namespace JobBoy\Bundle\JobBoyBundle\Flow\Domain\FlowManager\TransitionLoader;

use JobBoy\Flow\Domain\FlowManager\TransitionLoader\TransitionLoader;
use JobBoy\Flow\Domain\FlowManager\TransitionLoader\TransitionSetProvider;

class BundleTransitionLoaderAdapter
{

    private $transitionLoader;

    public function __construct(TransitionLoader $transitionLoader)
    {
        $this->transitionLoader = $transitionLoader;
    }

    public function load(TransitionSetProvider $transitionSetProvider): void
    {
        $this->transitionLoader->load($transitionSetProvider->get());
    }
}
