<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface IndexableResourceRepositoryInterface extends RepositoryInterface
{
    public function createIndexableCollectionQueryBuilder(): QueryBuilder;

    /**
     * @param list<scalar> $ids
     *
     * @return array<array-key, ResourceInterface>
     */
    public function findFromIndexScopeAndIds(IndexScope $indexScope, array $ids): array;
}
