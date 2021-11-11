<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusAlgoliaPlugin\DTO\ProductIndexScope;
use Sylius\Component\Core\Repository\ProductRepositoryInterface as BaseProductRepositoryInterface;

interface ProductRepositoryInterface extends BaseProductRepositoryInterface
{
    public function createQueryBuilderFromProductIndexScope(ProductIndexScope $productIndexScope): QueryBuilder;
}
