<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Client\RecommendationsClient;

use Algolia\AlgoliaSearch\RecommendClient;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

final class RecommendationsClient implements RecommendationsClientInterface
{
    private RecommendClient $recommendClient;

    public function __construct(RecommendClient $recommendClient)
    {
        $this->recommendClient = $recommendClient;
    }

    public function getFrequentlyBoughtTogether($product, string $index): array
    {
        if ($product instanceof ProductInterface) {
            $product = (string) $product->getId();
        }
        Assert::scalar($product);

        $this->recommendClient->getFrequentlyBoughtTogether([
            RecommendationRequest::createFrequentlyBoughtTogether($index, (string) $product)->toArray(),
        ]);

        // todo
        return [];
    }
}
