<?php


namespace JobBoy\Bundle\JobBoyBundle\DependencyInjection\Compiler;

use Assert\Assertion;
use JobBoy\Bundle\JobBoyBundle\DependencyInjection\Compiler\Util\CompilerPassUtil;
use JobBoy\Bundle\JobBoyBundle\Drivers\Doctrine\DoctrineDriverHelper;
use JobBoy\Bundle\JobBoyBundle\Drivers\Redis\RedisDriverHelper;
use JobBoy\Process\Domain\Entity\Infrastructure\TouchCallback\HydratableProcess as TouchCallbackHydratableProcess;
use JobBoy\Process\Domain\Entity\Infrastructure\TouchCallback\Process as TouchCallbackProcess;
use JobBoy\Process\Domain\Entity\Process;
use JobBoy\Process\Domain\Repository\Infrastructure\Doctrine\ProcessRepository as DoctrineProcessRepository;
use JobBoy\Process\Domain\Repository\Infrastructure\InMemory\ProcessRepository as InMemoryProcessRepository;
use JobBoy\Process\Domain\Repository\Infrastructure\Redis\ProcessRepository as RedisProcessRepository;
use JobBoy\Process\Domain\Repository\ProcessRepositoryInterface;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * I'm not sure, I don't remember. I think we are doing this in a CompilePass instead doing it
 * in the Extension to allow defining a ProcessRepository service in another bundle or in the app.
 * Maybe this is not necessary.
 */
class RegisterProcessRepositoryPass implements CompilerPassInterface
{
    const PROCESS_REPOSITORY_SERVICE_ID = 'jobboy.process_repository.service_id';
    const PROCESS_CLASS = 'jobboy.process.class';

    public function process(ContainerBuilder $container): void
    {

        $this->loadInMemoryProcessRepository($container);
        $this->loadDoctrineProcessRepository($container);
        $this->loadRedisProcessRepository($container);

        $this->setProcessClassIfMissing($container);

        $this->createProcessRepositoryAlias($container);
    }


    protected function loadInMemoryProcessRepository(ContainerBuilder $container)
    {
        if (!$container->hasParameter(self::PROCESS_REPOSITORY_SERVICE_ID)) {
            return;
        }

        $serviceId = $container->getParameter(self::PROCESS_REPOSITORY_SERVICE_ID);

        if ($serviceId === 'in_memory') {
            $container->setParameter(self::PROCESS_REPOSITORY_SERVICE_ID, InMemoryProcessRepository::class);
            $serviceId = $container->getParameter(self::PROCESS_REPOSITORY_SERVICE_ID);
        }

        if ($serviceId !== InMemoryProcessRepository::class) {
            return;
        }

        $this->setProcessClass($container, Process::class);
    }


    protected function loadDoctrineProcessRepository(ContainerBuilder $container)
    {
        if (!$container->hasParameter(self::PROCESS_REPOSITORY_SERVICE_ID)) {
            return;
        }

        if (!DoctrineDriverHelper::hasDriver()) {
            return;
        }

        $serviceId = $container->getParameter(self::PROCESS_REPOSITORY_SERVICE_ID);

        if ($serviceId === 'doctrine') {
            $container->setParameter(self::PROCESS_REPOSITORY_SERVICE_ID, DoctrineProcessRepository::class);
            $serviceId = $container->getParameter(self::PROCESS_REPOSITORY_SERVICE_ID);
        }

        if ($serviceId !== DoctrineProcessRepository::class) {
            return;
        }

        if (!$container->hasDefinition('doctrine.dbal.default_connection')) {
            throw new \InvalidArgumentException(sprintf(
                'To use %s as ProcessRepository you need the `doctrine.dbal.default_connection` service',
                DoctrineProcessRepository::class
            ));
        }

        $this->setProcessClass($container, TouchCallbackHydratableProcess::class);

        DoctrineDriverHelper::loadServices($container);
    }

    protected function loadRedisProcessRepository(ContainerBuilder $container)
    {
        if (!$container->hasParameter(self::PROCESS_REPOSITORY_SERVICE_ID)) {
            return;
        }

        if (!RedisDriverHelper::hasDriver()) {
            return;
        }

        $serviceId = $container->getParameter(RegisterProcessRepositoryPass::PROCESS_REPOSITORY_SERVICE_ID);

        if ($serviceId === 'redis') {
            $container->setParameter(self::PROCESS_REPOSITORY_SERVICE_ID, RedisProcessRepository::class);
            $serviceId = $container->getParameter(self::PROCESS_REPOSITORY_SERVICE_ID);
        }

        if ($serviceId !== RedisProcessRepository::class) {
            return;
        }


        if (!$container->hasParameter('jobboy.process_repository.redis.host')
            && !$container->hasParameter('jobboy.process_repository.redis.port')
        ) {
            throw new \InvalidArgumentException(sprintf(
                'To use %s as ProcessRepository you need to set `job_boy.process_repository.redis.host` config',
                RedisProcessRepository::class
            ));
        }


        $this->setProcessClass($container, TouchCallbackProcess::class);

        RedisDriverHelper::loadServices($container);
    }

    protected function setProcessClassIfMissing(ContainerBuilder $container)
    {
        $this->setProcessClass($container, Process::class);
    }


    protected function createProcessRepositoryAlias(ContainerBuilder $container)
    {
        if (!$container->hasParameter(self::PROCESS_REPOSITORY_SERVICE_ID)) {
            return;
        }

        $serviceId = $container->getParameter(self::PROCESS_REPOSITORY_SERVICE_ID);

        CompilerPassUtil::assertDefinitionImplementsInterface($container, $serviceId, ProcessRepositoryInterface::class);

        $container->setAlias(ProcessRepositoryInterface::class, new Alias($serviceId, true));
    }


    protected function setProcessClass(ContainerBuilder $container, string $processClass)
    {
        if (!$container->hasParameter(self::PROCESS_CLASS) || !$container->getParameter(self::PROCESS_CLASS)) {
            $container->setParameter(self::PROCESS_CLASS, $processClass);
            return;
        }

        try {
            Assertion::objectOrClass($container->getParameter(self::PROCESS_CLASS), $processClass);
        } catch (\InvalidArgumentException $e) {
            Assertion::subclassOf($container->getParameter(self::PROCESS_CLASS), $processClass);
        }
    }


}
