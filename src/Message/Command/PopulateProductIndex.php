<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Command;

use Setono\SyliusAlgoliaPlugin\DTO\ProductIndexScope;

final class PopulateProductIndex implements CommandInterface
{
    private ProductIndexScope $productIndexScope;

    public function __construct(ProductIndexScope $productIndexScope)
    {
        $this->productIndexScope = $productIndexScope;
    }

    public function getProductIndexScope(): ProductIndexScope
    {
        return $this->productIndexScope;
    }
}
