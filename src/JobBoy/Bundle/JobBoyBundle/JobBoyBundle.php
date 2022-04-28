<?php

namespace JobBoy\Bundle\JobBoyBundle;

use JobBoy\Bundle\JobBoyBundle\DependencyInjection\Helper\CompilerPassAdder as MainCompilerPassAdder;
use JobBoy\Bundle\JobBoyBundle\DependencyInjection\JobBoyExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class JobBoyBundle extends Bundle
{

    public function build(ContainerBuilder $containerBuilder)
    {
        parent::build($containerBuilder);

        $this->buildMain($containerBuilder);
        $this->buildDrivers($containerBuilder);

    }

    protected function getContainerExtensionClass()
    {
        return JobBoyExtension::class;
    }

    private function buildMain(ContainerBuilder $containerBuilder): void
    {
        $compilerPassAdder = new MainCompilerPassAdder();
        $compilerPassAdder->addTo($containerBuilder);
    }

    private function buildDrivers(ContainerBuilder $containerBuilder): void
    {
    }

}