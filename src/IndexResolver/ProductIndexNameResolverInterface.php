<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\IndexResolver;

use Setono\SyliusAlgoliaPlugin\DTO\ProductIndexScope;

interface ProductIndexNameResolverInterface
{
    /**
     * Will return the product index name for the given channel, locale, and currency or if null is provided,
     * from the channel context, locale context, and currency context respectively
     */
    public function resolve(
        string $channelCode = null,
        string $localeCode = null,
        string $currencyCode = null
    ): string;

    public function resolveFromScope(ProductIndexScope $productIndexScope): string;
}
