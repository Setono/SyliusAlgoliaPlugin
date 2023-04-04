<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Indexer;

final class CompositeIndexer extends AbstractIndexer
{
    /** @var list<IndexerInterface> */
    private array $indexers = [];

    public function add(IndexerInterface $indexer): void
    {
        $this->indexers[] = $indexer;
    }

    public function index($index): void
    {
        foreach ($this->indexers as $indexer) {
            if ($indexer->supports($index)) {
                $indexer->index($index);

                break;
            }
        }
    }

    public function indexResource($index, string $resource): void
    {
        foreach ($this->indexers as $indexer) {
            if ($indexer->supports($index)) {
                $indexer->indexResource($index, $resource);

                break;
            }
        }
    }

    public function indexEntitiesWithIds(array $ids, string $type): void
    {
        foreach ($this->indexers as $indexer) {
            if ($indexer->supports($type)) {
                $indexer->indexEntitiesWithIds($ids, $type);

                break;
            }
        }
    }

    public function removeEntitiesWithIds(array $ids, string $type): void
    {
        foreach ($this->indexers as $indexer) {
            if ($indexer->supports($type)) {
                $indexer->removeEntitiesWithIds($ids, $type);

                break;
            }
        }
    }

    public function supports($value): bool
    {
        foreach ($this->indexers as $indexer) {
            if ($indexer->supports($value)) {
                return true;
            }
        }

        return false;
    }
}
