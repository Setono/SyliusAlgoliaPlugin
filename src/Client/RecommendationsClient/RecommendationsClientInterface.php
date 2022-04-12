<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Client\RecommendationsClient;

use Sylius\Component\Core\Model\ProductInterface;

interface RecommendationsClientInterface
{
    /**
     * This method will return a list of products that are frequently bought together with the given $product
     *
     * @param int|string|ProductInterface $product
     *
     * @return list<ProductInterface>
     */
    public function getFrequentlyBoughtTogether($product, string $index): array;
}
