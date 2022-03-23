<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Indexer;

use Setono\SyliusAlgoliaPlugin\Document\Product;

final class ProductIndexer extends GenericIndexer
{
    public static function getDocumentClass(): string
    {
        return Product::class;
    }
}
