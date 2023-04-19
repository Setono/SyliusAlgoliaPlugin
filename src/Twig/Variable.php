<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Twig;

use Setono\SyliusAlgoliaPlugin\Twig\Variable\Autocomplete;

final class Variable
{
    private Autocomplete $autocomplete;

    private string $appId;

    private string $searchOnlyApiKey;

    public function __construct(Autocomplete $autocomplete, string $appId, string $searchOnlyApiKey)
    {
        $this->autocomplete = $autocomplete;
        $this->appId = $appId;
        $this->searchOnlyApiKey = $searchOnlyApiKey;
    }

    public function getAutocomplete(): Autocomplete
    {
        // todo before returning check if search is enabled in the configuration of the plugin

        return $this->autocomplete;
    }

    public function getAppId(): string
    {
        return $this->appId;
    }

    public function getSearchOnlyApiKey(): string
    {
        return $this->searchOnlyApiKey;
    }
}
