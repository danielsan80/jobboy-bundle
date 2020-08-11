<?php

namespace JobBoy\Bundle\JobBoyBundle\Step\Domain\StepManager;

use JobBoy\Step\Domain\StepManager\HasStepDataInterface;
use JobBoy\Step\Domain\StepManager\StepData;
use JobBoy\Step\Domain\StepManager\StepRegistry;

/**
 * You need a decorator (not only an adapter) because of the DIC service loading strategy
 */
class HasStepDataStepRegistryDecorator extends StepRegistry
{
    protected $stepRegistry;

    public function __construct(StepRegistry $stepRegistry)
    {
        $this->stepRegistry = $stepRegistry;
    }

    public function addHasStepData(HasStepDataInterface $hasStepData, ?int $position = null): void
    {
        $this->stepRegistry->add($hasStepData->stepData(), $position);
    }

    public function add(StepData $data, ?int $position = null): void
    {
        $this->stepRegistry->add($data, $position);
    }

    public function get(string $job): array
    {
        return $this->stepRegistry->get($job);
    }

}