<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DTO;

interface SettingsInterface
{
    /**
     * Returns an array of settings ready to pass to the \Algolia\AlgoliaSearch\SearchClient::setSettings() method
     */
    public function toArray(): array;
}
