<?php

namespace Tests\JobBoy\Bundle\JobBoyBundle\DependencyInjection;

use DD\Product\Application\DataStore\DataStore;
use DD\Product\Application\DataStore\DefaultDataStore;
use DD\Product\Bundle\DDProductBundle\DependencyInjection\DDProductExtension;
use DD\Product\Domain\Product\Facade\Factory\ProductFactory;
use DD\Product\Domain\Property\Model\Provider\PropertyProvider;
use JobBoy\Bundle\JobBoyBundle\DependencyInjection\JobBoyExtension;
use JobBoy\Process\Domain\Repository\Infrastructure\Redis\ProcessRepository as RedisProcessRepository;
use JobBoy\Process\Domain\Repository\ProcessRepositoryInterface;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class JobBoyExtensionTest extends AbstractExtensionTestCase
{

    protected function getContainerExtensions(): array
    {
        return [
            new JobBoyExtension()
        ];
    }

    /**
     * @test
     */
    public function it_fail_with_wrong_config()
    {

        $this->expectException(InvalidConfigurationException::class);

        $this->load([
            'process_repository' => 'a_process_repository_code',
            'unknown_key' => true
        ]);
    }

    /**
     * @test
     */
    public function it_fail_if_there_are_parameters_on_not_set_process_repository()
    {
        $this->markTestIncomplete('I cannot test this case until we have 2 drivers');

        $this->load([
            'process_repository' => [
                'name' => 'redis',
                'parameters' => [
                    'another_driver' => [
                        'host' => 'localhost',
                    ],
                    'redis' => [
                        'host' => 'locahost',
                        'namespace' => 'jobboy'
                    ]
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function it_can_manage_redis_process_repository()
    {
        $this->load([
            'process_repository' => [
                'name' => 'redis',
                'host' => 'locahost',
                'port' => 1111,
                'namespace' => 'jobboy',
            ]
        ]);

        $this->assertContainerBuilderHasParameter('jobboy.process_repository.service_id', 'redis');

        $this->assertContainerBuilderHasParameter('jobboy.process_repository.redis.host', 'locahost');
        $this->assertContainerBuilderHasParameter('jobboy.process_repository.redis.port', 1111);
        $this->assertContainerBuilderHasParameter('jobboy.process_repository.redis.namespace', 'jobboy');
    }

    /**
     * @test
     */
    public function it_can_manage_redis_process_repository_using_default_values()
    {
        $this->load([
            'process_repository' => [
                'name' => 'redis',
                'host' => 'locahost',
            ]
        ]);

        $this->assertContainerBuilderHasParameter('jobboy.process_repository.service_id', 'redis');
        $this->assertContainerBuilderHasParameter('jobboy.process_repository.redis.host', 'locahost');
        $this->assertContainerBuilderHasParameter('jobboy.process_repository.redis.port', 6379);
        $this->assertContainerBuilderHasParameter('jobboy.process_repository.redis.namespace', null);
    }


    /**
     * @test
     */
    public function exception_if_redis_host_is_missing()
    {
        $this->expectExceptionMessage('process_repository(redis) `host` is not set');
        $this->load([
            'process_repository' => [
                'name' => 'redis',
            ]
        ]);
    }

}
