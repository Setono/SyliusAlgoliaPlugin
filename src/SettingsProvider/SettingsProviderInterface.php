<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\SettingsProvider;

use Setono\SyliusAlgoliaPlugin\DTO\SettingsInterface;

interface SettingsProviderInterface
{
    public function getSettings(): SettingsInterface;
}
