<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Resolver\IndexName;

use Setono\SyliusAlgoliaPlugin\Config\Index;
use Setono\SyliusAlgoliaPlugin\Config\IndexRegistry;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Provider\IndexScope\IndexScopeProviderInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * This is a default index name resolver. This will give developers a better experience for simple scenarios
 * where an index name like 'products' or 'taxons' or 'pages' will suffice
 */
final class IndexNameResolver implements IndexNameResolverInterface
{
    private IndexRegistry $indexRegistry;

    private IndexScopeProviderInterface $indexScopeProvider;

    private string $environment;

    /** @var non-empty-string|null */
    private ?string $prefix;

    public function __construct(
        IndexRegistry $indexRegistry,
        IndexScopeProviderInterface $indexScopeProvider,
        string $environment,
        string $prefix = null
    ) {
        $this->indexRegistry = $indexRegistry;
        $this->indexScopeProvider = $indexScopeProvider;
        $this->environment = $environment;
        $this->prefix = '' === $prefix ? null : $prefix;
    }

    public function resolve($resource): string
    {
        return $this->resolveFromIndexScope($this->resolveIndexScope($resource));
    }

    public function resolveFromIndexScope(IndexScope $indexScope): string
    {
        $str = null !== $this->prefix ? ($this->prefix . '__') : '';

        $str .= $this->environment . '__' . $indexScope->index->name;

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

    public function supports(Index $index): bool
    {
        return true;
    }

    /**
     * @param class-string|ResourceInterface|Index $value
     */
    private function resolveIndexScope($value): IndexScope
    {
        if (!$value instanceof Index) {
            $value = $this->indexRegistry->getByResourceClass($value);
        }

        return $this->indexScopeProvider->getFromContext($value);
    }
}
