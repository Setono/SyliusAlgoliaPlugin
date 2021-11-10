<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Repository;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Setono\SyliusAlgoliaPlugin\Model\ResolvedProductIndexInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface as BaseProductRepositoryInterface;

interface ProductRepositoryInterface extends BaseProductRepositoryInterface
{
    public function createQueryBuilderForResolvedIndex(ResolvedProductIndexInterface $resolvedProductIndex): QueryBuilder;
}
