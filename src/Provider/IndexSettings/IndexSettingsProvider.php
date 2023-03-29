<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\IndexSettings;

use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Settings\IndexSettings;

final class IndexSettingsProvider implements IndexSettingsProviderInterface
{
    // todo implement an easier way to set settings decoupled from the document. This could be by dispatching an IndexSettingsEvent
    public function getSettings(IndexScope $indexScope): IndexSettings
    {
        return $indexScope->resource->documentClass::getDefaultSettings($indexScope);
    }
}
