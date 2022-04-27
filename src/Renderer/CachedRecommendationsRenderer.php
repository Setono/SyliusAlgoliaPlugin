<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Renderer;

use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class CachedRecommendationsRenderer implements RecommendationsRendererInterface
{
    private RecommendationsRendererInterface $decoratedRecommendationsRenderer;

    private CacheInterface $cachePool;

    private int $cacheTtl;

    public function __construct(
        RecommendationsRendererInterface $decoratedRecommendationsRenderer,
        CacheInterface $cachePool,
        int $cacheTtl
    ) {
        $this->decoratedRecommendationsRenderer = $decoratedRecommendationsRenderer;
        $this->cachePool = $cachePool;
        $this->cacheTtl = $cacheTtl;
    }

    public function renderFrequentlyBoughtTogether(ProductInterface $product, string $index, int $max = 10): string
    {
        $cacheKey = sprintf('%s_%s', (string) $product->getId(), $index);

        /** @psalm-suppress ArgumentTypeCoercion */
        return $this->cachePool->get($cacheKey, function (ItemInterface $item) use ($product, $index, $max): string {
            $item->expiresAfter($this->cacheTtl);

            return $this->decoratedRecommendationsRenderer->renderFrequentlyBoughtTogether($product, $index, $max);
        });
    }
}
