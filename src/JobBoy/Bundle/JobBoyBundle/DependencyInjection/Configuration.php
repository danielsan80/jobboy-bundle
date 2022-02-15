<?php

namespace JobBoy\Bundle\JobBoyBundle\DependencyInjection;

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

        $rootNode
            ->children()
                ->scalarNode('instance_code')
                    ->defaultValue(null)
                    ->info('A code for this instance. NULL by default, it means each instance has a code calculated from the path of the installation. At now it is used by the locks.')
                ->end()
                ->scalarNode('process_repository')
                    ->defaultValue('in_memory')
                    ->info('a service definition id implementing JobBoy\Process\Domain\Repository\ProcessRepositoryInterface')
                ->end()
                ->scalarNode('process_class')
                    ->info('a FQCN of the Process class to use in the JobBoy\Process\Domain\Factory\ProcessFactory')
                ->end()
                ->arrayNode('redis')
                    ->info('used in case of RedisProcessRepository, ignored otherwise')
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
                ->arrayNode('api')
                    ->info('used if JobBoyApi module is available')
                    ->children()
                        ->scalarNode('required_role')
                            ->info('the role required to call the JobBoy api')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
