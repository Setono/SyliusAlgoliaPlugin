<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\IndexScope;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;

final class DefaultIndexScopeProvider implements IndexScopeProviderInterface
{
    public function getAll(IndexableResource $indexableResource): iterable
    {
        yield new IndexScope($indexableResource);
    }

    public function getFromContext(IndexableResource $indexableResource): IndexScope
    {
        return new IndexScope($indexableResource);
    }

    public function getFromChannelAndLocaleAndCurrency(
        IndexableResource $indexableResource,
        string $channelCode = null,
        string $localeCode = null,
        string $currencyCode = null
    ): IndexScope {
        return $this->getFromContext($indexableResource);
    }

    public function supports(IndexableResource $indexableResource): bool
    {
        return true;
    }
}
