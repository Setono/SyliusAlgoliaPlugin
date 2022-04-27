<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Client\RecommendationsClient;

/**
 * todo include other available parameters, see: https://www.algolia.com/doc/api-reference/api-methods/get-recommendations/
 */
class RecommendationRequest
{
    public const MODEL_BOUGHT_TOGETHER = 'bought-together';

    public const MODEL_RELATED_PRODUCTS = 'related-products';

    private string $indexName;

    private string $objectId;

    private string $model;

    private int $maxRecommendations;

    public function __construct(string $indexName, string $objectId, string $model, int $maxRecommendations)
    {
        $this->indexName = $indexName;
        $this->objectId = $objectId;
        $this->model = $model;
        $this->maxRecommendations = $maxRecommendations;
    }

    public static function createFrequentlyBoughtTogether(string $indexName, string $objectId, int $maxRecommendations): self
    {
        return new self($indexName, $objectId, self::MODEL_BOUGHT_TOGETHER, $maxRecommendations);
    }

    public static function createRelatedProducts(string $indexName, string $objectId, int $maxRecommendations): self
    {
        return new self($indexName, $objectId, self::MODEL_RELATED_PRODUCTS, $maxRecommendations);
    }

    public function toArray(): array
    {
        return [
            'indexName' => $this->indexName,
            'objectID' => $this->objectId,
            'model' => $this->model,
            'maxRecommendations' => $this->maxRecommendations,
        ];
    }
}
