<?php

namespace JobBoy\Bundle\JobBoyBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use JobBoy\Bundle\JobBoyBundle\DependencyInjection\Helper\ApiHelper;

class JobBoyExtension extends Extension
{
    const REDIS_DEFAULT_PORT = 6379;
    const REDIS_DEFAULT_NAMESPACE = null;

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {

        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $this->readInstanceCode($config, $container);
        $this->readProcessRepository($config, $container);
        $this->readProcessClass($config, $container);
        $this->readRedis($config, $container);
        $this->loadServices($container);
        $this->loadApiServices($container);


    }

    protected function readInstanceCode(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('jobboy.instance_code', $config['instance_code']);
    }


    protected function readProcessRepository(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('jobboy.process_repository.service_id', $config['process_repository']);
    }


    protected function readProcessClass(array $config, ContainerBuilder $container): void
    {
        if (isset($config['process_class'])) {
            $container->setParameter('jobboy.process.class', $config['process_class']);
        }
    }


    protected function readRedis(array $config, ContainerBuilder $container): void
    {
        if (isset($config['redis']['host'])) {
            $container->setParameter('jobboy.process_repository.redis.host', $config['redis']['host']);
            if (isset($config['redis']['port'])) {
                $container->setParameter('jobboy.process_repository.redis.port', $config['redis']['port']);
            } else {
                $container->setParameter('jobboy.process_repository.redis.port', self::REDIS_DEFAULT_PORT);
            }
            if (isset($config['redis']['namespace'])) {
                $container->setParameter('jobboy.process_repository.redis.namespace', $config['redis']['namespace']);
            } else {
                $container->setParameter('jobboy.process_repository.redis.namespace', self::REDIS_DEFAULT_NAMESPACE);
            }
        }
    }

    protected function loadServices(ContainerBuilder $container): void
    {

        $locator = new FileLocator(__DIR__ . '/../Resources/config');

        $loader = new DirectoryLoader($container, $locator);
        $resolver = new LoaderResolver([
            new YamlFileLoader($container, $locator),
            $loader,
        ]);
        $loader->setResolver($resolver);

        $loader->load('services');
    }

    protected function loadApiServices(ContainerBuilder $container): void
    {
        if (!ApiHelper::hasApi()) {
            return;
        }

        ApiHelper::loadServices($container);
    }

}