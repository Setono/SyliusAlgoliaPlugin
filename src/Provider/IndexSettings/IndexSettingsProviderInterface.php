<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\IndexSettings;

use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Registry\SupportsResourceAwareInterface;
use Setono\SyliusAlgoliaPlugin\Settings\SettingsInterface;

interface IndexSettingsProviderInterface extends SupportsResourceAwareInterface
{
    public function getSettings(IndexScope $indexScope): SettingsInterface;
}
