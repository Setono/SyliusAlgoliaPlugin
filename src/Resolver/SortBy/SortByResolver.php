<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Resolver\SortBy;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\Resolver\IndexName\IndexNameResolverInterface;
use Setono\SyliusAlgoliaPlugin\Resolver\ReplicaIndexName\ReplicaIndexNameResolverInterface;

final class SortByResolver implements SortByResolverInterface
{
    private IndexNameResolverInterface $indexNameResolver;

    private ReplicaIndexNameResolverInterface $replicaIndexNameResolver;

    public function __construct(
        IndexNameResolverInterface $indexNameResolver,
        ReplicaIndexNameResolverInterface $replicaIndexNameResolver
    ) {
        $this->indexNameResolver = $indexNameResolver;
        $this->replicaIndexNameResolver = $replicaIndexNameResolver;
    }

    public function resolveFromIndexableResource(IndexableResource $indexableResource): array
    {
        $indexName = $this->indexNameResolver->resolve($indexableResource);

        $sortBys = [
            new SortBy('setono_sylius_algolia.ui.sort_by.relevance', $indexName),
        ];

        foreach ($indexableResource->documentClass::getSortableAttributes() as $attribute => $order) {
            $sortBys[] = new SortBy(
                sprintf('setono_sylius_algolia.ui.sort_by.%s_%s', $attribute, $order),
                $this->replicaIndexNameResolver->resolveFromIndexNameAndSortableAttribute(
                    $indexName,
                    $attribute,
                    $order
                )
            );
        }

        return $sortBys;
    }
}
