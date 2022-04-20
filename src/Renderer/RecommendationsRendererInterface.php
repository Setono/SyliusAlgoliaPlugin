<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Renderer;

use Sylius\Component\Core\Model\ProductInterface;

interface RecommendationsRendererInterface
{
    public function renderFrequentlyBoughtTogether(ProductInterface $product, string $index, int $max = 10): string;
}
