<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Resolver\SortBy;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;

interface SortByResolverInterface
{
    /**
     * @param string|null $locale if null, the locale context will be used to retrieve the locale
     *
     * @return list<SortBy>
     */
    public function resolveFromIndexableResource(IndexableResource $indexableResource, string $locale = null): array;
}
