<?php
declare(strict_types=1);

namespace JobBoy\Bundle\JobBoyBundle\Drivers\Redis;

use Assert\Assertion;
use JobBoy\Process\Application\Driver\Infrastructure\Redis\DependencyInjection\Helper\ServiceLoader;
use JobBoy\Process\Application\Driver\Infrastructure\Redis\RedisDriver;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RedisDriverHelper
{

    static public function hasDriver(): bool
    {
        return class_exists(RedisDriver::class);
    }

    static public function assertHasDriver(): void
    {
        Assertion::true(self::hasDriver(), 'The Redis driver is not available.');
    }

    static public function loadServices(ContainerBuilder $container): void
    {
        (new ServiceLoader())->load($container);
    }

}
