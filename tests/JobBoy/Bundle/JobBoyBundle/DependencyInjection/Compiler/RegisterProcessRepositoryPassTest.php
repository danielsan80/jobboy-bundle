<?php

namespace Tests\JobBoy\Bundle\JobBoyBundle\DependencyInjection\Compiler;

use Doctrine\DBAL\Connection;
use JobBoy\Bundle\JobBoyBundle\DependencyInjection\Compiler\RegisterProcessRepositoryPass;
use JobBoy\Process\Domain\Entity\Infrastructure\TouchCallback\HydratableProcess as TouchCallbackHydratableProcess;
use JobBoy\Process\Domain\Entity\Process;
use JobBoy\Process\Domain\Repository\Infrastructure\Redis\ProcessRepository as RedisProcessRepository;
use JobBoy\Process\Domain\Repository\Infrastructure\Doctrine\ProcessRepository as DoctrineProcessRepository;
use JobBoy\Process\Domain\Repository\Infrastructure\InMemory\ProcessRepository as InMemoryProcessRepository;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use JobBoy\Process\Domain\Entity\Infrastructure\TouchCallback\Process as TouchCallbackProcess;
use Tests\JobBoy\Bundle\JobBoyBundle\DependencyInjection\Compiler\Test\CustomProcessRepository;

class RegisterProcessRepositoryPassTest extends AbstractCompilerPassTestCase
{

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterProcessRepositoryPass());
    }


    /**
     * @test
     */
    public function in_memory_process_repository()
    {
        $this->setDefinition(InMemoryProcessRepository::class, new Definition(InMemoryProcessRepository::class));
        $this->setParameter(RegisterProcessRepositoryPass::PROCESS_REPOSITORY_SERVICE_ID, 'in_memory');

        $this->compile();

        $this->assertContainerBuilderHasParameter(RegisterProcessRepositoryPass::PROCESS_REPOSITORY_SERVICE_ID, InMemoryProcessRepository::class);
        $this->assertContainerBuilderHasParameter(RegisterProcessRepositoryPass::PROCESS_CLASS, Process::class);

        $this->assertContainerBuilderNotHasService(DoctrineProcessRepository::class, DoctrineProcessRepository::class);
        $this->assertContainerBuilderNotHasService(RedisProcessRepository::class, RedisProcessRepository::class);
    }

    /**
     * @test
     */
    public function doctrine_process_repository()
    {
        $this->setDefinition('doctrine.dbal.default_connection', new Definition(Connection::class));
        $this->setParameter(RegisterProcessRepositoryPass::PROCESS_REPOSITORY_SERVICE_ID, 'doctrine');

        $this->compile();

        $this->assertContainerBuilderHasParameter(RegisterProcessRepositoryPass::PROCESS_REPOSITORY_SERVICE_ID, DoctrineProcessRepository::class);
        $this->assertContainerBuilderHasParameter(RegisterProcessRepositoryPass::PROCESS_CLASS, TouchCallbackHydratableProcess::class);
        $this->assertContainerBuilderHasService(DoctrineProcessRepository::class, DoctrineProcessRepository::class);
    }

    /**
     * @test
     */
    public function doctrine_process_repository_will_not_work_without_a_dbal_connection()
    {

        $this->setParameter(RegisterProcessRepositoryPass::PROCESS_REPOSITORY_SERVICE_ID, 'doctrine');

        $this->expectExceptionMessage(sprintf(
            'To use %s as ProcessRepository you need the `doctrine.dbal.default_connection` service',
            DoctrineProcessRepository::class
        ));
        $this->compile();
    }

    /**
     * @test
     */
    public function redis_process_repository()
    {
        $this->setParameter('jobboy.process_repository.redis.host', 'localhost');
        $this->setParameter('jobboy.process_repository.redis.port', 6379);
        $this->setParameter(RegisterProcessRepositoryPass::PROCESS_REPOSITORY_SERVICE_ID, 'redis');

        $this->compile();

        $this->assertContainerBuilderHasParameter(RegisterProcessRepositoryPass::PROCESS_REPOSITORY_SERVICE_ID, RedisProcessRepository::class);
        $this->assertContainerBuilderHasParameter(RegisterProcessRepositoryPass::PROCESS_CLASS, TouchCallbackProcess::class);
        $this->assertContainerBuilderHasService(RedisProcessRepository::class, RedisProcessRepository::class);
    }

    /**
     * @test
     */
    public function redis_process_repository_will_not_work_without_parameters()
    {
        $this->setParameter(RegisterProcessRepositoryPass::PROCESS_REPOSITORY_SERVICE_ID, 'redis');

        $this->expectExceptionMessage(sprintf(
            'To use %s as ProcessRepository you need to set `job_boy.process_repository.redis.host` config',
            RedisProcessRepository::class
        ));
        $this->compile();
    }

    /**
     * @test
     */
    public function it_set_the_default_Process_class_if_missing()
    {
        $this->setDefinition(CustomProcessRepository::class, new Definition(CustomProcessRepository::class));
        $this->setParameter(RegisterProcessRepositoryPass::PROCESS_REPOSITORY_SERVICE_ID, CustomProcessRepository::class);

        $this->compile();

        $this->assertContainerBuilderHasParameter(RegisterProcessRepositoryPass::PROCESS_REPOSITORY_SERVICE_ID, CustomProcessRepository::class);
        $this->assertContainerBuilderHasParameter(RegisterProcessRepositoryPass::PROCESS_CLASS, Process::class);
    }

}
