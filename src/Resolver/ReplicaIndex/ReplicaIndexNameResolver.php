<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Resolver\ReplicaIndex;

final class ReplicaIndexNameResolver implements ReplicaIndexNameResolverInterface
{
    public function resolveFromIndexNameWithSortableAttribute(
        string $indexName,
        string $attribute,
        string $order
    ): string {
        return sprintf('%s__%s__%s', $indexName, $attribute, $order);
    }
}
