<?php

namespace JobBoy\Bundle\JobBoyBundle\DependencyInjection\Compiler;

use Assert\Assertion;
use JobBoy\Bundle\JobBoyBundle\Flow\Domain\FlowManager\TransitionLoader\RegisterTransitionSetProvidersTransitionLoaderDecorator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterTransitionSetProvidersPass implements CompilerPassInterface
{
    const REGISTRY = RegisterTransitionSetProvidersTransitionLoaderDecorator::class;
    const TAG = 'jobboy.flow.transition_set_provider';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::REGISTRY)) {
            return;
        }

        $registry = $container->findDefinition(self::REGISTRY);

        $services = $container->findTaggedServiceIds(self::TAG);
        foreach ($services as $serviceId => $data) {
            Assertion::count($data, 1);
            $data = $data[0];

            $registry->addMethodCall('loadProvider', [new Reference($serviceId)]);
        }
    }
}
