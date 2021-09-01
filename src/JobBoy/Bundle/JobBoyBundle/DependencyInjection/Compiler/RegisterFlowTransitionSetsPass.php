<?php

namespace JobBoy\Bundle\JobBoyBundle\DependencyInjection\Compiler;

use Assert\Assertion;
use JobBoy\Flow\Domain\FlowManager\TransitionLoader\TransitionLoader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterFlowTransitionSetsPass implements CompilerPassInterface
{
    const REGISTRY = TransitionLoader::class;
    const TAG = 'jobboy.flow.transition_set_provider';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::REGISTRY)) {
            return;
        }

        $registry = $container->findDefinition(self::REGISTRY);

        $services = $container->findTaggedServiceIds(self::TAG);
        foreach ($services as $serviceId => $data) {
            Assertion::count($data, 1);
            $data = $data[0];

            $registry->addMethodCall('load', [new Reference($serviceId)]);
        }
    }
}
