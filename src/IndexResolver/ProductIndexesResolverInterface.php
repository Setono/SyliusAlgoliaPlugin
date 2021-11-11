<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\IndexResolver;

use Setono\SyliusAlgoliaPlugin\DTO\ProductIndexScope;

interface ProductIndexesResolverInterface
{
    /**
     * @return iterable<ProductIndexScope>
     */
    public function resolve(): iterable;
}
