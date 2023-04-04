<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Model\IndexableInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface IndexableResourceRepositoryInterface extends RepositoryInterface
{
    public function createIndexableCollectionQueryBuilder(): QueryBuilder;

    /**
     * @param list<mixed> $ids
     *
     * @return list<IndexableInterface>
     */
    public function findFromIndexScopeAndIds(IndexScope $indexScope, array $ids): array;
}
