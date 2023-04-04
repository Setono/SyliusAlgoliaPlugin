<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\IndexScope;

use Setono\SyliusAlgoliaPlugin\Config\Index;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;

final class DefaultIndexScopeProvider implements IndexScopeProviderInterface
{
    public function getAll(Index $index): iterable
    {
        yield new IndexScope($index);
    }

    public function getFromContext(Index $index): IndexScope
    {
        return new IndexScope($index);
    }

    public function getFromChannelAndLocaleAndCurrency(
        Index $index,
        string $channelCode = null,
        string $localeCode = null,
        string $currencyCode = null
    ): IndexScope {
        return $this->getFromContext($index);
    }

    public function supports(Index $index): bool
    {
        return true;
    }
}
