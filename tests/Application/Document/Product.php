<?php
declare(strict_types=1);


namespace Tests\Setono\SyliusAlgoliaPlugin\Application\Document;


use Setono\SyliusAlgoliaPlugin\Document\Product as BaseProduct;

final class Product extends BaseProduct
{
    public static function getSortableAttributes(): array
    {
        return [
            'price' => 'asc',
        ];
    }
}
