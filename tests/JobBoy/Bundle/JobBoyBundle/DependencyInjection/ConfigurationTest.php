<?php

namespace Tests\JobBoy\Bundle\JobBoyBundle\DependencyInjection;

use JobBoy\Bundle\JobBoyBundle\DependencyInjection\Configuration;
use JobBoy\Bundle\JobBoyBundle\DependencyInjection\JobBoyExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    protected function getContainerExtension(): ExtensionInterface
    {
        return new JobBoyExtension();
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }

    /**
     * @test
     */
    public function no_config_is_not_allowed()
    {

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The child config "process_repository" under "job_boy" must be configured: a process repository for the Process data persistence');
        $this->assertProcessedConfigurationEquals(
            [],
            [
                __DIR__ . '/files/no_config.yaml'
            ]
        );
    }


    /**
     * @test
     */
    public function an_empty_config_is_not_allowed()
    {

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The child config "process_repository" under "job_boy" must be configured: a process repository for the Process data persistence');
        $this->assertProcessedConfigurationEquals(
            [],
            [
                __DIR__ . '/files/empty_config.yaml'
            ]
        );
    }

    /**
     * @test
     */
    public function you_can_pass_the_process_repository_name_as_string_value()
    {

        $this->assertProcessedConfigurationEquals(
            [
                'process_repository' => [
                    'name' => 'a_process_repository_code',
                ],
            ],
            [
                __DIR__ . '/files/process_repository_as_string.yaml'
            ]
        );
    }

    /**
     * @test
     */
    public function you_can_pass_the_process_repository_as_array_key_value()
    {

        $this->assertProcessedConfigurationEquals(
            [
                'process_repository' => [
                    'name' => 'a_process_repository_code',
                ],
            ],
            [
                __DIR__ . '/files/process_repository_as_array.yaml'
            ]
        );
    }

    /**
     * @test
     */
    public function you_can_add_parameters_for_the_redis_process_repository()
    {

        $this->assertProcessedConfigurationEquals([
            'process_repository' => [
                'name' => 'redis',
                'parameters' => [
                    'redis' => [
                        'host' => 'localhost',
                        'port' => 6379,
                        'namespace' => 'jobboy',
                    ]
                ],
            ],
        ], [
            __DIR__ . '/files/process_repository_redis.yaml'
        ]);

    }


    /**
     * @test
     */
    public function you_can_pass_an_instance_code()
    {

        $this->assertProcessedConfigurationEquals(
            [
                'instance_code' => 'an_instance_code',
                'process_repository' => [
                    'name' => 'a_process_repository',
                ]
            ],
            [
                __DIR__ . '/files/instance_code.yaml'
            ]
        );
    }

    /**
     * @test
     */
    public function you_can_pass_a_process_class()
    {

        $this->assertProcessedConfigurationEquals(
            [
                'process_class' => 'Path\To\Process',
                'process_repository' => [
                    'name' => 'a_process_repository',
                ]
            ],
            [
                __DIR__ . '/files/process_class.yaml'
            ]
        );
    }

    /**
     * @test
     */
    public function you_can_specify_a_required_role_for_the_api()
    {

        $this->assertProcessedConfigurationEquals(
            [
                'api' => [
                    'required_role' => 'ROLE_API',
                ],
                'process_repository' => [
                    'name' => 'a_process_repository',
                ]
            ],
            [
                __DIR__ . '/files/api.yaml'
            ]
        );
    }

}
