<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\IndexSettings;

use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Settings\IndexSettings;

interface IndexSettingsProviderInterface
{
    public function getSettings(IndexScope $indexScope): IndexSettings;
}
