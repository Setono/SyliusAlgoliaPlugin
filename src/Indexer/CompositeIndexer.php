<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Indexer;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Sylius\Component\Resource\Model\ResourceInterface;

final class CompositeIndexer implements IndexerInterface
{
    /** @var list<IndexerInterface> */
    private array $indexers = [];

    public function add(IndexerInterface $indexer): void
    {
        $this->indexers[] = $indexer;
    }

    public function indexResource(IndexableResource $resource): void
    {
        foreach ($this->indexers as $indexer) {
            if ($indexer->supports($resource)) {
                $indexer->indexResource($resource);

                break;
            }
        }
    }

    public function indexEntity(ResourceInterface $entity): void
    {
        foreach ($this->indexers as $indexer) {
            if ($indexer->supports($entity)) {
                $indexer->indexEntity($entity);

                break;
            }
        }
    }

    public function indexEntities(array $entities, IndexableResource $indexableResource = null): void
    {
        foreach ($this->indexers as $indexer) {
            /**
             * TODO
             * We need these suppressions until https://github.com/vimeo/psalm/issues/9581 is fixed
             *
             * @psalm-suppress MixedArgument,InvalidArrayAccess
             */
            if ($indexer->supports($indexableResource ?? $entities[0])) {
                $indexer->indexEntities($entities, $indexableResource);

                break;
            }
        }
    }

    public function removeEntity(ResourceInterface $entity): void
    {
        foreach ($this->indexers as $indexer) {
            if ($indexer->supports($entity)) {
                $indexer->removeEntity($entity);

                break;
            }
        }
    }

    public function removeEntities(array $entities, IndexableResource $indexableResource = null): void
    {
        foreach ($this->indexers as $indexer) {
            /**
             * TODO
             * We need these suppressions until https://github.com/vimeo/psalm/issues/9581 is fixed
             *
             * @psalm-suppress MixedArgument,InvalidArrayAccess
             */
            if ($indexer->supports($indexableResource ?? $entities[0])) {
                $indexer->removeEntities($entities, $indexableResource);

                break;
            }
        }
    }

    public function supports($resource): bool
    {
        foreach ($this->indexers as $indexer) {
            if ($indexer->supports($resource)) {
                return true;
            }
        }

        return false;
    }
}
