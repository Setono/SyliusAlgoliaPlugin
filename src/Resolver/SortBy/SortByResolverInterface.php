<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Resolver\SortBy;

use Setono\SyliusAlgoliaPlugin\Config\Index;

interface SortByResolverInterface
{
    /**
     * @param string|null $locale if null, the locale context will be used to retrieve the locale
     *
     * @return list<SortBy>
     */
    public function resolveFromIndexableResource(Index $index, string $locale = null): array;
}
