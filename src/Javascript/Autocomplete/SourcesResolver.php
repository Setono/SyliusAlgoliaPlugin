<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Javascript\Autocomplete;

use Setono\SyliusAlgoliaPlugin\Config\IndexRegistry;
use Setono\SyliusAlgoliaPlugin\Resolver\IndexName\IndexNameResolverInterface;

final class SourcesResolver implements SourcesResolverInterface
{
    private IndexRegistry $indexRegistry;

    private IndexNameResolverInterface $indexNameResolver;

    /**
     * These are the search indexes defined in the configuration of the plugin (inside setono_sylius_algolia.search.indexes)
     *
     * @var list<string>
     */
    private array $searchIndexes;

    /**
     * @param list<string> $searchIndexes
     */
    public function __construct(IndexRegistry $indexRegistry, IndexNameResolverInterface $indexNameResolver, array $searchIndexes)
    {
        $this->indexRegistry = $indexRegistry;
        $this->indexNameResolver = $indexNameResolver;
        $this->searchIndexes = $searchIndexes;
    }

    public function getSources(): array
    {
        $sources = [];
        foreach ($this->searchIndexes as $searchIndex) {
            $index = $this->indexRegistry->getByName($searchIndex);
            $sources[] = new Source($searchIndex, $this->indexNameResolver->resolve($index));
        }

        return $sources;
    }
}
