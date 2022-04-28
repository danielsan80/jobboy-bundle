<?php
declare(strict_types=1);

namespace JobBoy\Bundle\JobBoyBundle\Drivers\Doctrine;


use Assert\Assertion;
use JobBoy\Process\Application\Driver\Infrastructure\Doctrine\DependencyInjection\Helper\ServiceLoader;
use JobBoy\Process\Application\Driver\Infrastructure\Doctrine\DoctrineDriver;
use Symfony\Component\DependencyInjection\ContainerBuilder;


class DoctrineDriverHelper
{
    static public function hasDriver(): bool
    {
        return class_exists(DoctrineDriver::class);
    }

    static public function assertHasDriver(): void
    {
        Assertion::true(self::hasDriver(), 'The Doctrine driver is not available.');
    }

    static public function loadServices(ContainerBuilder $container): void
    {
        (new ServiceLoader())->load($container);
    }

}
