<?php

namespace JobBoy\Bundle\JobBoyBundle\Flow\Domain\FlowManager;

use JobBoy\Flow\Domain\FlowManager\HasNode;
use JobBoy\Flow\Domain\FlowManager\Node;
use JobBoy\Flow\Domain\FlowManager\Transition;
use JobBoy\Flow\Domain\FlowManager\TransitionRegistry;
/**
 * You need a decorator (not only an adapter) because of the DIC service loading strategy
 */
class BundleTransitionRegistryAdapter
{
    protected $transitionRegistry;

    public function __construct(TransitionRegistry $transitionRegistry)
    {
        $this->transitionRegistry = $transitionRegistry;
    }

    public function addEntry(HasNode $hasNode): void
    {
        $this->transitionRegistry->add(Transition::createEntry($hasNode->node()));
    }

    public function addNodeChangeFrom(string $fromNode, HasNode $toNode, string $on): void
    {
        $to = $toNode->node();
        $this->transitionRegistry->add(Transition::createNodeChange(Node::create($to->job(), $fromNode), $to, $on));
    }

    public function addNodeChangeTo(HasNode $fromNode, string $toNode, string $on): void
    {
        $from = $fromNode->node();
        $this->transitionRegistry->add(Transition::createNodeChange($from, Node::create($from->job(), $toNode), $on));
    }

    public function addExit(HasNode $hasNode, string $on): void
    {
        $this->transitionRegistry->add(Transition::createExit($hasNode->node(), $on));
    }

}