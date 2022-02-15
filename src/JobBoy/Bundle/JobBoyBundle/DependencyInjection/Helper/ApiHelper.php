<?php
declare(strict_types=1);


namespace JobBoy\Bundle\JobBoyBundle\DependencyInjection\Helper;

use Assert\Assertion;
use DD\Product\Bundle\DDProductBundle\Drivers\Couch\DependencyInjection\Helper\ServiceLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ApiHelper
{
    private static $JobBoyApi = 'JobBoy\Process\Api\JobBoyApi';
    private static $ServiceLoader = 'JobBoy\Process\Api\DependencyInjection\Helper\ServiceLoader';


    static public function hasApi(): bool
    {
        return class_exists(self::$JobBoyApi);
    }

    static public function assertHasApi(): void
    {
        Assertion::true(self::hasApi(), 'The Api module is not available.');
    }

    static public function loadServices(ContainerBuilder $container): void
    {
        (new self::$ServiceLoader())->load($container);
    }

}
