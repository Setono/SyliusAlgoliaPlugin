<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusAlgoliaPlugin\Settings;

use PHPUnit\Framework\TestCase;
use Setono\SyliusAlgoliaPlugin\Settings\Settings;

/**
 * @covers \Setono\SyliusAlgoliaPlugin\Settings\Settings
 */
final class SettingsTest extends TestCase
{
    /**
     * @test
     */
    public function it_converts_to_array(): void
    {
        $settings = new Settings();
        $settings->distinct = 1;
        $settings->attributesToHighlight[] = 'attr1';

        self::assertSame([
            'attributesToHighlight' => ['attr1'],
            'distinct' => 1,
        ], $settings->toArray());
    }
}
