<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\SettingsProvider;

use Setono\SyliusAlgoliaPlugin\DTO\IndexSettings;

/**
 * This service returns the default settings for an index. Decorate this service to your own needs
 */
final class DefaultSettingsProvider implements SettingsProviderInterface
{
    public function getSettings(): IndexSettings
    {
        $settings = new IndexSettings();
        $settings->attributesForFaceting = [
            'filterOnly(taxonCodes)',
        ];

        return $settings;
    }
}
