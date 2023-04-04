<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Indexer;

use Setono\SyliusAlgoliaPlugin\Model\IndexableInterface;

abstract class AbstractIndexer implements IndexerInterface
{
    public function indexEntity(IndexableInterface $entity): void
    {
        $this->indexEntitiesWithIds([$entity->getId()], get_class($entity));
    }

    public function indexEntityWithId($id, string $type): void
    {
        $this->indexEntitiesWithIds([$id], $type);
    }

    public function indexEntities(array $entities): void
    {
        if ([] === $entities) {
            return;
        }

        $ids = [];
        $type = null;
        foreach ($entities as $entity) {
            if ($type === null) {
                $type = get_class($entity);
            }

            if ($type !== get_class($entity)) {
                throw new \InvalidArgumentException('All the entities must be of the same type');
            }

            /** @psalm-suppress MixedAssignment */
            $ids[] = $entity->getId();
        }

        $this->indexEntitiesWithIds($ids, $type);
    }

    public function removeEntity(IndexableInterface $entity): void
    {
        $this->removeEntityWithId($entity->getId(), get_class($entity));
    }

    public function removeEntityWithId($id, string $type): void
    {
        $this->removeEntitiesWithIds([$id], $type);
    }

    public function removeEntities(array $entities): void
    {
        // todo this is a duplication of the indexEntities
        if ([] === $entities) {
            return;
        }

        $ids = [];
        $type = null;
        foreach ($entities as $entity) {
            if ($type === null) {
                $type = get_class($entity);
            }

            if ($type !== get_class($entity)) {
                throw new \InvalidArgumentException('All the entities must be of the same type');
            }

            /** @psalm-suppress MixedAssignment */
            $ids[] = $entity->getId();
        }

        $this->removeEntitiesWithIds($ids, $type);
    }
}
