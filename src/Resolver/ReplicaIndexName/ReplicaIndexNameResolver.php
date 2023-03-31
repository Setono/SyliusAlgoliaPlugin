<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Resolver\ReplicaIndexName;

final class ReplicaIndexNameResolver implements ReplicaIndexNameResolverInterface
{
    public function resolveFromIndexNameAndExistingValue(string $indexName, string $existingValue): string
    {
        return sprintf('%s__%s', $indexName, $existingValue);
    }

    public function resolveFromIndexNameAndSortableAttribute(
        string $indexName,
        string $attribute,
        string $order
    ): string {
        return sprintf('%s__%s_%s', $indexName, $attribute, $order);
    }
}
