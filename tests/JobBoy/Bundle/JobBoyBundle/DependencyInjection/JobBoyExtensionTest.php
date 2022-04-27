<?php

namespace Tests\JobBoy\Bundle\JobBoyBundle\DependencyInjection;

use DD\Product\Application\DataStore\DataStore;
use DD\Product\Application\DataStore\DefaultDataStore;
use DD\Product\Bundle\DDProductBundle\DependencyInjection\DDProductExtension;
use DD\Product\Domain\Product\Facade\Factory\ProductFactory;
use DD\Product\Domain\Property\Model\Provider\PropertyProvider;
use JobBoy\Bundle\JobBoyBundle\DependencyInjection\JobBoyExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

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

    /**
     * @test
     */
    public function it_can_manage_process_repository_as_service_id()
    {
        $this->load([
            'process_repository' => [
                'name' => 'a_process_repository.service_id',
            ]
        ]);

        $this->assertContainerBuilderHasParameter('jobboy.process_repository.service_id', 'a_process_repository.service_id');
    }

    /**
     * @test
     */
    public function it_can_manage_instance_code()
    {
        $this->load([
            'instance_code' => 'an_instance_code',
            'process_repository' => [
                'name' => 'a_process_repository.service_id',
            ]
        ]);

        $this->assertContainerBuilderHasParameter('jobboy.instance_code', 'an_instance_code');
    }

    /**
     * @test
     */
    public function it_can_manage_instance_code_using_default_value()
    {
        $this->load([
            'process_repository' => [
                'name' => 'a_process_repository.service_id',
            ]
        ]);

        $this->assertContainerBuilderHasParameter('jobboy.instance_code', null);
    }

    /**
     * @test
     */
    public function it_can_manage_process_class()
    {
        $this->load([
            'process_class' => 'Path\To\Process',
            'process_repository' => [
                'name' => 'a_process_repository.service_id',
            ]
        ]);

        $this->assertContainerBuilderHasParameter('jobboy.process.class', 'Path\To\Process');
    }

    /**
     * @test
     */
    public function it_can_manage_process_class_using_default_value()
    {
        $this->load([
            'process_repository' => [
                'name' => 'a_process_repository.service_id',
            ]
        ]);

        $this->expectNotToPerformAssertions();

        try {
            $this->container->getParameter('jobboy.process.class');
            $this->fail('Parameter "jobboy.process.class" should not be set');
        } catch (ParameterNotFoundException $e) {
        }
    }

    /**
     * @test
     */
    public function it_can_manage_api()
    {
        $this->load([
            'api' => [
                'required_role' => 'ROLE_API',
            ],
            'process_repository' => [
                'name' => 'a_process_repository.service_id',
            ]
        ]);

        $this->assertContainerBuilderHasParameter('jobboy.api.required_role', 'ROLE_API');
    }

    /**
     * @test
     */
    public function it_can_manage_api_using_default_value()
    {
        $this->load([
            'process_repository' => [
                'name' => 'a_process_repository.service_id',
            ]
        ]);

        $this->assertContainerBuilderHasParameter('jobboy.api.required_role', 'ROLE_JOBBOY');
    }


}
