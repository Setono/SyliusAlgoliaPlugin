<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Twig;

final class AlgoliaVariable
{
    private string $appId;

    private string $searchOnlyApiKey;

    public function __construct(string $appId, string $searchOnlyApiKey)
    {
        $this->appId = $appId;
        $this->searchOnlyApiKey = $searchOnlyApiKey;
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
