<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Resolver\SortBy;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;

interface SortByResolverInterface
{
    /**
     * @return list<SortBy>
     */
    public function resolveFromIndexableResource(IndexableResource $indexableResource): array;
}
