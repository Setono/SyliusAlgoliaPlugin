<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Resolver\ReplicaIndex;

interface ReplicaIndexNameResolverInterface
{
    public function resolveFromIndexNameWithSortableAttribute(string $indexName, string $attribute, string $order): string;
}
