<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider;

use Setono\SyliusAlgoliaPlugin\DTO\ProductIndexScope;

interface ProductIndexScopesProviderInterface
{
    /**
     * @return iterable<ProductIndexScope>
     */
    public function getProductIndexScopes(): iterable;
}
