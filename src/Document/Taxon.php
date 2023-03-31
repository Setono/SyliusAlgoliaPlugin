<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Document;

use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Settings\IndexSettings;

/**
 * Should not be final, so it's easier for plugin users to extend it and add more properties
 */
class Taxon extends Document implements UrlAwareInterface
{
    public ?string $name = null;

    public ?string $url = null;

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public static function getDefaultSettings(IndexScope $indexScope): IndexSettings
    {
        $settings = parent::getDefaultSettings($indexScope);

        $settings->searchableAttributes = ['name'];

        return $settings;
    }
}
