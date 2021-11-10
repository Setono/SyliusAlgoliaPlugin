<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusAlgoliaPlugin\Application\Repository;

use Setono\SyliusAlgoliaPlugin\Repository\ProductRepositoryInterface;
use Setono\SyliusAlgoliaPlugin\Repository\ProductRepositoryTrait;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;

class ProductRepository extends BaseProductRepository implements ProductRepositoryInterface
{
    use ProductRepositoryTrait;
}
