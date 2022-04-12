<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\IndexNameResolver;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResourceCollection;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Provider\IndexScope\IndexScopeProviderInterface;
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

    private IndexScopeProviderInterface $indexScopeProvider;

    private string $environment;

    private InflectorInterface $inflector;

    public function __construct(
        IndexableResourceCollection $indexableResourceCollection,
        IndexScopeProviderInterface $indexScopeProvider,
        string $environment,
        InflectorInterface $inflector = null
    ) {
        $this->indexableResourceCollection = $indexableResourceCollection;
        $this->indexScopeProvider = $indexScopeProvider;
        $this->environment = $environment;
        $this->inflector = $inflector ?? new EnglishInflector();
    }

    public function resolve($resource): string
    {
        return $this->resolveFromIndexScope($this->resolveIndexScope($resource));
    }

    public function resolveFromIndexScope(IndexScope $indexScope): string
    {
        $str = $this->inflector->pluralize($indexScope->resource->shortName)[0];

        if (null !== $indexScope->channelCode) {
            $str .= '__' . $indexScope->channelCode;
        }

        if (null !== $indexScope->localeCode) {
            $str .= '__' . $indexScope->localeCode;
        }

        if (null !== $indexScope->currencyCode) {
            $str .= '__' . $indexScope->currencyCode;
        }

        $str .= '__' . $this->environment;

        return strtolower($str);
    }

    public function supports(IndexableResource $indexableResource): bool
    {
        return true;
    }

    /**
     * @param class-string<ResourceInterface>|ResourceInterface|IndexableResource $resource
     */
    private function resolveIndexScope($resource): IndexScope
    {
        if (!$resource instanceof IndexableResource) {
            $resource = $this->indexableResourceCollection->getByClass($resource);
        }

        return $this->indexScopeProvider->getFromContext($resource);
    }
}
