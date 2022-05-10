<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusAlgoliaPlugin\Settings;

use PHPUnit\Framework\TestCase;
use Setono\SyliusAlgoliaPlugin\Settings\IndexSettings;

/**
 * @covers \Setono\SyliusAlgoliaPlugin\Settings\IndexSettings
 */
final class IndexSettingsTest extends TestCase
{
    /**
     * @test
     */
    public function it_converts_to_array(): void
    {
        $settings = new IndexSettings();
        $settings->searchableAttributes[] = 'attr1';

        self::assertSame([
            'searchableAttributes' => ['attr1'],
        ], $settings->toArray());
    }
}
