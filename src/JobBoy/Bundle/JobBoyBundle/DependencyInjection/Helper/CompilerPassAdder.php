<?php
declare(strict_types=1);

namespace JobBoy\Bundle\JobBoyBundle\DependencyInjection\Helper;

use JobBoy\Bundle\JobBoyBundle\DependencyInjection\Compiler\RegisterEventListenersPass;
use JobBoy\Bundle\JobBoyBundle\DependencyInjection\Compiler\RegisterHasNodeTransitionsPass;
use JobBoy\Bundle\JobBoyBundle\DependencyInjection\Compiler\RegisterProcessHandlersPass;
use JobBoy\Bundle\JobBoyBundle\DependencyInjection\Compiler\RegisterProcessRepositoryPass;
use JobBoy\Bundle\JobBoyBundle\DependencyInjection\Compiler\RegisterStepsPass;
use JobBoy\Bundle\JobBoyBundle\DependencyInjection\Compiler\RegisterTransitionSetProvidersPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CompilerPassAdder
{

    /** @var CompilerPassInterface[] */
    private $compilerPasses = [];

    public function __construct()
    {
        $this->createCompilerPasses();
    }


    private function createCompilerPasses()
    {
        $this->compilerPasses[] = new RegisterProcessRepositoryPass();
        $this->compilerPasses[] = new RegisterProcessHandlersPass();

        $this->compilerPasses[] = new RegisterStepsPass();

        $this->compilerPasses[] = new RegisterHasNodeTransitionsPass();
        $this->compilerPasses[] = new RegisterTransitionSetProvidersPass();

        $this->compilerPasses[] = new RegisterEventListenersPass();
    }

    public function addTo(ContainerBuilder $container)
    {
        foreach ($this->compilerPasses as $pass) {
            $container->addCompilerPass($pass);
        }
    }

    public function process(ContainerBuilder $container)
    {
        foreach ($this->compilerPasses as $pass) {
            $pass->process($container);
        }
    }
}
