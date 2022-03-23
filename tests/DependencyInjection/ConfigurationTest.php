<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusAlgoliaPlugin\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Setono\SyliusAlgoliaPlugin\DependencyInjection\Configuration;

/**
 * See examples of tests and configuration options here: https://github.com/SymfonyTest/SymfonyConfigTest
 */
final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }

    /**
     * @test
     */
    public function values_are_invalid_if_required_value_is_not_provided(): void
    {
        $this->assertConfigurationIsInvalid(
            [
                [], // no values at all
            ],
            '/The child (config|node) "app_id" (under|at path) "setono_sylius_algolia" must be configured/',
            true
        );
    }

    /**
     * @test
     */
    public function processed_value_contains_required_value(): void
    {
        $this->assertProcessedConfigurationEquals([
            ['app_id' => 'first_app_id', 'search_only_api_key' => 'first_search_only_api_key', 'admin_api_key' => 'first_admin_api_key'],
            ['app_id' => 'last_app_id', 'search_only_api_key' => 'last_search_only_api_key', 'admin_api_key' => 'last_admin_api_key'],
        ], [
            'app_id' => 'last_app_id',
            'search_only_api_key' => 'last_search_only_api_key',
            'admin_api_key' => 'last_admin_api_key',
            'indexable_resources' => [],
        ]);
    }
}
