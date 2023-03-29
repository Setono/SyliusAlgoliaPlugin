<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Indexer;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Sylius\Component\Resource\Model\ResourceInterface;

interface IndexerInterface
{
    /**
     * This method will index all entities for a given indexable resource
     */
    public function indexResource(IndexableResource $resource): void;

    /**
     * Will index a single entity
     */
    public function indexEntity(ResourceInterface $entity): void;

    /**
     * Will index multiple entities
     *
     * If the entities are scalars, then the $indexableResource must be set (else we can't deduce the entity type)
     *
     * @template T of IndexableResource|null
     *
     * @param T $indexableResource
     * @param (T is null ? non-empty-list<ResourceInterface> : non-empty-list<scalar|ResourceInterface>) $entities
     */
    public function indexEntities(array $entities, IndexableResource $indexableResource = null): void;

    public function removeEntity(ResourceInterface $entity): void;

    /**
     * Will remove multiple entities
     *
     * If the entities are scalars, then the $indexableResource must be set (else we can't deduce the entity type)
     *
     * @template T of IndexableResource|null
     *
     * @param T $indexableResource
     * @param (T is null ? non-empty-list<ResourceInterface> : non-empty-list<scalar|ResourceInterface>) $entities
     */
    public function removeEntities(array $entities, IndexableResource $indexableResource = null): void;

    /**
     * @param ResourceInterface|IndexableResource $resource
     */
    public function supports($resource): bool;
}
