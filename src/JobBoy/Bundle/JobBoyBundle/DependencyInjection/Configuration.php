<?php

namespace JobBoy\Bundle\JobBoyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration definition.
 */
class Configuration implements ConfigurationInterface
{
    const REDIS_DEFAULT_PORT = 6379;
    const REDIS_DEFAULT_NAMESPACE = null;

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('job_boy');

        if (\method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('job_boy');
        }

        $this->addInstanceCodeSection($rootNode);
        $this->addProcessRepositorySection($rootNode);
        $this->addProcessClassSection($rootNode);
        $this->addApiSection($rootNode);

        return $treeBuilder;
    }

    private function addInstanceCodeSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->scalarNode('instance_code')
                    ->info('A code for this instance. NULL by default, it means each instance has a code calculated from the path of the installation. At now it is used by the locks.')
                ->end()
        ;
    }

    private function addProcessRepositorySection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('process_repository')
                    ->info('a process repository for the Process data persistence')
                    ->isRequired()
                    ->children()
                        ->scalarNode('name')
                            ->info('A service id implementing JobBoy\Process\Domain\Repository\ProcessRepositoryInterface OR a known process repository code')
                            ->isRequired()
                        ->end()
                        ->arrayNode('parameters')
                            ->info('The parameters for the chosen process repository code')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;


        $this->expandName($this->find($node,'process_repository'));
        $this->expandParameters($this->find($node, 'process_repository'));

        $this->addRedisProcessRepositoryParameters($node);
    }

    private function addProcessClassSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->scalarNode('process_class')
                    ->info('a FQCN of the Process class to use in the JobBoy\Process\Domain\Factory\ProcessFactory')
                ->end()
            ->end()
        ;
    }

    private function addApiSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('api')
                    ->info('used if JobBoyApi module is available')
                    ->children()
                        ->scalarNode('required_role')
                            ->info('the role required to call the JobBoy api')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addRedisProcessRepositoryParameters(ArrayNodeDefinition $node): void
    {
        $this->find($node, 'process_repository.parameters')
            ->children()
                ->arrayNode('redis')
                    ->info('The parameters for the `redis` process repository')
                    ->children()
                        ->scalarNode('host')
                            ->info('the Redis host')
                        ->end()
                        ->scalarNode('port')
                            ->info('the Redis port')
                            ->defaultValue(self::REDIS_DEFAULT_PORT)
                        ->end()
                        ->scalarNode('namespace')
                            ->info('the namespace where to save processes on Redis')
                            ->defaultValue(self::REDIS_DEFAULT_NAMESPACE)
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }


    private function expandName(ArrayNodeDefinition $node): void
    {
        $node
            ->beforeNormalization()
            ->ifString()
            ->then(function ($v) {
                return [
                    'name' => $v
                ];
            })
            ->end()
        ;
    }

    private function expandParameters(ArrayNodeDefinition $node): void
    {
        $node
            ->beforeNormalization()
            ->ifArray()
            ->then(function ($v) {
                $v2 = [
                    'name' => $v['name'],
                ];
                if (isset($v['parameters'])) {
                    $v2['parameters'] = $v['parameters'];
                }
                foreach ($v as $key => $value) {
                    if ($key =='name' || $key =='parameters') {
                        continue;
                    }
                    $v2['parameters'][$v['name']][$key] = $value;
                }
                return $v2;
            })
            ->end()
        ;
    }

    /**
     * Porting from Sf 4.4+ ArrayNodeDefinition version
     * When possible remove this method and
     * $this->find($node, 'a.sub.node.path') => $node->find('a.sub.node.path')
     */
    protected function find(NodeDefinition $mainNode, string $nodePath): NodeDefinition
    {
        if (method_exists($mainNode, 'find')) {
            return $mainNode->find($nodePath);
        }

        $mainNodeRef = new \ReflectionClass($mainNode);
        $property = $mainNodeRef->getProperty('children');
        $property->setAccessible(true);
        $children = $property->getValue($mainNode);

        $firstPathSegment = (false === $pathSeparatorPos = strpos($nodePath, '.'))
            ? $nodePath
            : substr($nodePath, 0, $pathSeparatorPos);

        if (null === $node = ($children[$firstPathSegment] ?? null)) {
            throw new \RuntimeException(sprintf('Node with name "%s" does not exist in the current node.', $firstPathSegment));
        }

        if (false === $pathSeparatorPos) {
            return $node;
        }

        return $this->find($node, substr($nodePath, $pathSeparatorPos + \strlen('.')));
    }

}
