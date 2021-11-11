<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\IndexResolver;

use Setono\SyliusAlgoliaPlugin\Model\ResolvedProductIndexInterface;

interface ProductIndexesResolverInterface
{
    /**
     * @return iterable|ResolvedProductIndexInterface[]
     */
    public function resolve(): iterable;
}
