<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Resolver\IndexName;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResourceRegistry;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Provider\IndexScope\IndexScopeProviderInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\String\Inflector\EnglishInflector;
use Symfony\Component\String\Inflector\InflectorInterface;

/**
 * This is a default index name resolver. This will give developers a better experience for simple scenarios
 * where an index name like 'products' or 'taxons' or 'pages' will suffice
 */
final class IndexNameResolver implements IndexNameResolverInterface
{
    private IndexableResourceRegistry $indexableResourceRegistry;

    private IndexScopeProviderInterface $indexScopeProvider;

    private string $environment;

    /** @var non-empty-string|null */
    private ?string $prefix;

    private InflectorInterface $inflector;

    public function __construct(
        IndexableResourceRegistry $indexableResourceRegistry,
        IndexScopeProviderInterface $indexScopeProvider,
        string $environment,
        string $prefix = null,
        InflectorInterface $inflector = null
    ) {
        $this->indexableResourceRegistry = $indexableResourceRegistry;
        $this->indexScopeProvider = $indexScopeProvider;
        $this->environment = $environment;
        $this->prefix = '' === $prefix ? null : $prefix;
        $this->inflector = $inflector ?? new EnglishInflector();
    }

    public function resolve($resource): string
    {
        return $this->resolveFromIndexScope($this->resolveIndexScope($resource));
    }

    public function resolveFromIndexScope(IndexScope $indexScope): string
    {
        $str = null !== $this->prefix ? ($this->prefix . '__') : '';
        $str .= $this->environment . '__' . $this->inflector->pluralize($indexScope->resource->shortName)[0];

        if (null !== $indexScope->channelCode) {
            $str .= '__' . $indexScope->channelCode;
        }

        if (null !== $indexScope->localeCode) {
            $str .= '__' . $indexScope->localeCode;
        }

        if (null !== $indexScope->currencyCode) {
            $str .= '__' . $indexScope->currencyCode;
        }

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
            $resource = $this->indexableResourceRegistry->getByClass($resource);
        }

        return $this->indexScopeProvider->getFromContext($resource);
    }
}
