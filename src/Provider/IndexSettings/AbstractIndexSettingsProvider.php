<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\IndexSettings;

use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Settings\IndexSettings;

abstract class AbstractIndexSettingsProvider implements IndexSettingsProviderInterface
{
    public function getSettings(IndexScope $indexScope): IndexSettings
    {
        $settings = new IndexSettings();

        if (null !== $indexScope->localeCode) {
            $language = substr($indexScope->localeCode, 0, 2);
            $settings->queryLanguages = [$language];
            $settings->indexLanguages = [$language];
        }

        // 60 is a very good number because it is dividable by 6, 5, 4, 3, and 2 which means that your responsive views
        // are going to look good no matter how many products you show per row (with a max of 6 per row though ;))
        $settings->hitsPerPage = 60;

        return $settings;
    }
}
