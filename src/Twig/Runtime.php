<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Twig;

use Setono\SyliusAlgoliaPlugin\Renderer\RecommendationsRendererInterface;
use Setono\SyliusAlgoliaPlugin\Resolver\IndexName\IndexNameResolverInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class Runtime implements RuntimeExtensionInterface
{
    private RecommendationsRendererInterface $recommendationsRenderer;

    private IndexNameResolverInterface $indexNameResolver;

    public function __construct(
        RecommendationsRendererInterface $recommendationsRenderer,
        IndexNameResolverInterface $indexNameResolver
    ) {
        $this->recommendationsRenderer = $recommendationsRenderer;
        $this->indexNameResolver = $indexNameResolver;
    }

    public function renderFrequentlyBoughtTogether(ProductInterface $product, int $max = 10): string
    {
        return $this->recommendationsRenderer->renderFrequentlyBoughtTogether(
            $product,
            $this->indexNameResolver->resolve($product),
            $max
        );
    }

    public function renderRelatedProducts(ProductInterface $product, int $max = 10): string
    {
        return $this->recommendationsRenderer->renderRelatedProducts(
            $product,
            $this->indexNameResolver->resolve($product),
            $max
        );
    }
}
