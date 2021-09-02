<?php
declare(strict_types=1);

namespace JobBoy\Bundle\JobBoyBundle\Flow\Domain\FlowManager\TransitionLoader;

use JobBoy\Flow\Domain\FlowManager\Node;
use JobBoy\Flow\Domain\FlowManager\Transition;
use JobBoy\Flow\Domain\FlowManager\TransitionLoader\TransitionLoader;
use JobBoy\Flow\Domain\FlowManager\TransitionRegistry;

class WithTransitionLoaderTransitionRegistryDecorator implements TransitionRegistry
{

    private $transitionRegistry;
    private $transitionLoader;

    public function __construct(TransitionRegistry $transitionRegistry, TransitionLoader $transitionLoader)
    {
        $this->transitionRegistry = $transitionRegistry;
        $this->transitionLoader = $transitionLoader;
    }

    public function add(Transition $transition): void
    {
        $this->transitionRegistry->add($transition);
    }

    public function getEntry(string $job): Transition
    {
        return $this->transitionRegistry->getEntry($job);
    }

    public function get(Node $from, string $on): Transition
    {
        return $this->transitionRegistry->get($from, $on);
    }
}
