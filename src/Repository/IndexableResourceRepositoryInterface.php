<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Sylius\Component\Core\Repository\ProductRepositoryInterface as BaseProductRepositoryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface IndexableResourceRepositoryInterface extends BaseProductRepositoryInterface
{
    public function createIndexableCollectionQueryBuilder(): QueryBuilder;

    /**
     * @param list<scalar> $ids
     *
     * @return array<array-key, ResourceInterface>
     */
    public function findFromIndexScopeAndIds(IndexScope $indexScope, array $ids): array;
}
