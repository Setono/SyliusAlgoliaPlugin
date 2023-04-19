<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Javascript\Autocomplete;

use Setono\SyliusAlgoliaPlugin\Settings\Settings;

final class Source
{
    public string $sourceId;

    public string $indexName;

    public Settings $params;

    public function __construct(string $sourceId, string $indexName, Settings $params = null)
    {
        $this->sourceId = $sourceId;
        $this->indexName = $indexName;
        $this->params = $params ?? new Settings();
    }
}
