<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\Recommendations;

use Sylius\Component\Core\Model\ProductInterface;

interface RecommendationsProviderInterface
{
    /**
     * @return iterable<ProductInterface>
     */
    public function getFrequentlyBoughtTogether(ProductInterface $product, string $index, int $max = 10): iterable;
}
