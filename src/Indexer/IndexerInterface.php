<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Indexer;

use Setono\SyliusAlgoliaPlugin\Config\Index;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\Exception\NonExistingIndexException;
use Setono\SyliusAlgoliaPlugin\Exception\NonExistingResourceException;
use Setono\SyliusAlgoliaPlugin\Model\IndexableInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface IndexerInterface
{
    /**
     * Will index _all_ resources on the given index
     *
     * @param string|Index $index
     *
     * @throws NonExistingIndexException if the $index is a string and the index doesn't exist
     */
    public function index($index): void;

    /**
     * This method will index all entities for a given indexable resource on the given index
     *
     * @param string|Index $index
     *
     * @throws NonExistingIndexException if the $index is a string and the index doesn't exist
     * @throws NonExistingResourceException if the $resource doesn't exist on the given $index
     */
    public function indexResource($index, string $resource): void;

    /**
     * Will index a single entity
     */
    public function indexEntity(IndexableInterface $entity): void;

    /**
     * This method will index an entity with the given $id of the given $type
     *
     * @param mixed $id
     * @param class-string<IndexableInterface> $type
     */
    public function indexEntityWithId($id, string $type): void;

    /**
     * @param list<IndexableInterface> $entities
     */
    public function indexEntities(array $entities): void;

    /**
     * This method will index all the entities matching the $ids of the given $type
     *
     * @param list<mixed> $ids
     * @param class-string<IndexableInterface> $type
     */
    public function indexEntitiesWithIds(array $ids, string $type): void;

    public function removeEntity(IndexableInterface $entity): void;

    /**
     * This method will remove an entity with the given $id of the given $type
     *
     * @param mixed $id
     * @param class-string<IndexableInterface> $type
     */
    public function removeEntityWithId($id, string $type): void;

    /**
     * @param list<IndexableInterface> $entities
     */
    public function removeEntities(array $entities): void;

    /**
     * This method will remove all the entities matching the $ids of the given $type
     *
     * @param list<mixed> $ids
     * @param class-string<IndexableInterface> $type
     */
    public function removeEntitiesWithIds(array $ids, string $type): void;

    /**
     * todo This doesn't work. Should be refactored somehow. Maybe allow users to select the indexer on an index?
     *
     * @param ResourceInterface|Index|IndexableResource|string $value
     */
    public function supports($value): bool;
}
