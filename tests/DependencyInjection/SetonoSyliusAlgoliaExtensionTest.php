<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusAlgoliaPlugin\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Setono\SyliusAlgoliaPlugin\DependencyInjection\SetonoSyliusAlgoliaExtension;

/**
 * See examples of tests and configuration options here: https://github.com/SymfonyTest/SymfonyDependencyInjectionTest
 */
final class SetonoSyliusAlgoliaExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new SetonoSyliusAlgoliaExtension(),
        ];
    }

    protected function getMinimalConfiguration(): array
    {
        return [
            'app_id' => 'app_id',
            'search_only_api_key' => 'search_only_api_key',
            'admin_api_key' => 'admin_api_key',
        ];
    }

    /**
     * @test
     */
    public function after_loading_the_correct_parameter_has_been_set(): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('setono_sylius_algolia.app_id', 'app_id');
        $this->assertContainerBuilderHasParameter('setono_sylius_algolia.search_only_api_key', 'search_only_api_key');
        $this->assertContainerBuilderHasParameter('setono_sylius_algolia.admin_api_key', 'admin_api_key');
    }
}
