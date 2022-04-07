<?php

namespace JobBoy\Bundle\JobBoyBundle\DependencyInjection\Compiler;

use JobBoy\Process\Domain\Repository\Infrastructure\InMemory\ProcessRepository;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterProcessRepositoryPassTest extends AbstractCompilerPassTestCase
{

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterProcessRepositoryPass());
    }


    /**
     * @test
     */
    public function in_memory()
    {
        $inMemoryProcessRepositoryDef = new Definition(ProcessRepository::class);
        $this->setDefinition(ProcessRepository::class, $inMemoryProcessRepositoryDef);
        $this->setParameter(RegisterProcessRepositoryPass::PROCESS_REPOSITORY_SERVICE_ID, 'in_memory');

        $this->compile();

        $this->assertContainerBuilderHasParameter(RegisterProcessRepositoryPass::PROCESS_REPOSITORY_SERVICE_ID, ProcessRepository::class);

    }
}
