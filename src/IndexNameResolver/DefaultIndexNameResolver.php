<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\IndexNameResolver;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResourceCollection;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\String\Inflector\EnglishInflector;
use Symfony\Component\String\Inflector\InflectorInterface;

/**
 * This is a default index name resolver. This will give developers a better experience for simple scenarios
 * where an index name like 'products' or 'taxons' or 'pages' will suffice
 */
final class DefaultIndexNameResolver implements IndexNameResolverInterface
{
    private IndexableResourceCollection $indexableResourceCollection;

    private InflectorInterface $inflector;

    public function __construct(
        IndexableResourceCollection $indexableResourceCollection,
        InflectorInterface $inflector = null
    ) {
        $this->indexableResourceCollection = $indexableResourceCollection;
        $this->inflector = $inflector ?? new EnglishInflector();
    }

    public function resolve($resource): string
    {
        return $this->resolveFromResource($resource);
    }

    public function resolveFromIndexScope(IndexScope $indexScope, $resource): string
    {
        return $this->resolveFromResource($resource);
    }

    public function supports($resource): bool
    {
        return true;
    }

    /**
     * @param IndexableResource|ResourceInterface $resource
     */
    private function resolveFromResource($resource): string
    {
        $indexableResource = $this->getIndexableResource($resource);

        return $this->inflector->pluralize($indexableResource->shortName)[0];
    }

    /**
     * @param IndexableResource|ResourceInterface $resource
     */
    private function getIndexableResource($resource): IndexableResource
    {
        return $resource instanceof IndexableResource ? $resource : $this->indexableResourceCollection->getByClass($resource);
    }
}
