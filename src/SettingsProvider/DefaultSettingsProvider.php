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
        $settings->searchableAttributes = ['code', 'name'];
        $settings->attributesForFaceting = [
            'filterOnly(taxonCodes)', 'onSale', 'price',
            'taxons.lvl0', 'taxons.lvl1', 'taxons.lvl2', // todo these should somehow be dynamically generated
        ];
        $settings->customRanking = ['desc(createdAt)'];
        $settings->disablePrefixOnAttributes = ['code'];
        $settings->ignorePlurals = true; // remember to set query languages
        $settings->allowTyposOnNumericTokens = false;

        // 60 is a very good number because it is dividable by 6, 5, 4, 3, and 2 which means that your responsive views
        // are going to look good no matter how many products you show per row (with a max of 6 per row though ;))
        $settings->hitsPerPage = 60;

        return $settings;
    }
}
