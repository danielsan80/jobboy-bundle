<?php

namespace JobBoy\Bundle\JobBoyBundle\DependencyInjection\Compiler;

use Assert\Assertion;
use JobBoy\Bundle\JobBoyBundle\Flow\Domain\FlowManager\HasNodeTransitionRegistryDecorator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterFlowPass implements CompilerPassInterface
{
    const DEFAULT_ON = 'done';
    const DEFAULT_POSITION = 100;

    const REGISTRY = HasNodeTransitionRegistryDecorator::class;
    const TAG_ENTRY = 'jobboy.flow.entry';
    const TAG_NODE_CHANGE = 'jobboy.flow.node_change';
    const TAG_EXIT = 'jobboy.flow.exit';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::REGISTRY)) {
            return;
        }

        $registry = $container->findDefinition(self::REGISTRY);

        $services = $container->findTaggedServiceIds(self::TAG_ENTRY);
        foreach ($services as $serviceId => $data) {
            Assertion::count($data, 1);
            $data = $data[0];
            if (!isset($data['position'])) {
                $data['position'] = self::DEFAULT_POSITION;
            }

            $registry->addMethodCall('addEntry', [new Reference($serviceId)]);
        }

        $services = $container->findTaggedServiceIds(self::TAG_EXIT);
        foreach ($services as $serviceId => $data) {
            Assertion::count($data, 1);
            $data = $data[0];
            if (!isset($data['on'])) {
                $data['on'] = self::DEFAULT_ON;
            }

            $registry->addMethodCall('addExit', [new Reference($serviceId), $data['on']]);
        }

        $services = $container->findTaggedServiceIds(self::TAG_NODE_CHANGE);
        foreach ($services as $serviceId => $tagData) {
            foreach ($tagData as $data) {
                if (!isset($data['from']) && !isset($data['to'])) {
                    throw new \LogicException('You MUST set a `from` OR a `to` node');
                }

                if (!isset($data['on'])) {
                    $data['on'] = self::DEFAULT_ON;
                }

                if (isset($data['from'])) {
                    $registry->addMethodCall('addNodeChangeFrom', [new Reference($serviceId), $data['from'], $data['on']]);
                }
                if (isset($data['to'])) {
                    $registry->addMethodCall('addNodeChangeTo', [new Reference($serviceId), $data['to'], $data['on']]);
                }
            }
        }
    }
}
