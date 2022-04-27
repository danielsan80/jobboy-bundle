<?php

namespace JobBoy\Bundle\JobBoyBundle\DependencyInjection;

use Assert\Assertion;
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
    const API_DEFAULT_REQUIRED_ROLE = 'ROLE_JOBBOY';

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {

        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $this->assertProcessRepositoryParametersAreValid($config);

        $this->readInstanceCode($config, $container);
        $this->readProcessRepositoryName($config, $container);
        $this->readProcessClass($config, $container);
        $this->readRedisProcessRepository($config, $container);
        $this->readApi($config, $container);
        $this->loadServices($container);
        $this->loadApiServices($container);


    }

    protected function readInstanceCode(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('jobboy.instance_code', $config['instance_code']??null);
    }


    protected function readProcessRepositoryName(array $config, ContainerBuilder $container): void
    {
        Assertion::true(isset($config['process_repository']['name']), 'The config key "process_repository.name" is required');
        $container->setParameter('jobboy.process_repository.service_id', $config['process_repository']['name']);
    }


    protected function readProcessClass(array $config, ContainerBuilder $container): void
    {
        if (isset($config['process_class'])) {
            $container->setParameter('jobboy.process.class', $config['process_class']??null);
        }
    }


    protected function readRedisProcessRepository(array $config, ContainerBuilder $container): void
    {
        if ($config['process_repository']['name'] === 'redis') {
            Assertion::true(isset($config['process_repository']['parameters']['redis']['host']),'process_repository(redis) `host` is not set');
            $container->setParameter('jobboy.process_repository.redis.host', $config['process_repository']['parameters']['redis']['host']);
            if (isset($config['process_repository']['parameters']['redis']['port'])) {
                $container->setParameter('jobboy.process_repository.redis.port', $config['process_repository']['parameters']['redis']['port']);
            } else {
                $container->setParameter('jobboy.process_repository.redis.port', self::REDIS_DEFAULT_PORT);
            }
            if (isset($config['process_repository']['parameters']['redis']['namespace'])) {
                $container->setParameter('jobboy.process_repository.redis.namespace', $config['process_repository']['parameters']['redis']['namespace']);
            } else {
                $container->setParameter('jobboy.process_repository.redis.namespace', self::REDIS_DEFAULT_NAMESPACE);
            }
        }
    }

    protected function readApi(array $config, ContainerBuilder $container): void
    {
        $requiredRole = $config['api']['required_role']??self::API_DEFAULT_REQUIRED_ROLE;
        $container->setParameter('jobboy.api.required_role', $requiredRole);
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

    private function assertProcessRepositoryParametersAreValid(array $config): void
    {
        if (!isset($config['process_repository']['parameters'])) {
            return;
        }
        foreach ($config['process_repository']['parameters'] as $key => $value) {
            Assertion::eq($key, $config['process_repository']['name'], 'You cannot set parameters for a process repository you are not using');
        }
    }

}