<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Client\RecommendationsClient;

use Algolia\AlgoliaSearch\RecommendClient;
use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\Model\ObjectIdAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Webmozart\Assert\Assert;

final class RecommendationsClient implements RecommendationsClientInterface
{
    private RecommendClient $recommendClient;

    private DenormalizerInterface $denormalizer;

    /** @var class-string<Document> */
    private string $productDocumentClass;

    /**
     * @param class-string<Document> $productDocumentClass
     */
    public function __construct(RecommendClient $recommendClient, DenormalizerInterface $denormalizer, string $productDocumentClass)
    {
        $this->recommendClient = $recommendClient;
        $this->denormalizer = $denormalizer;
        $this->productDocumentClass = $productDocumentClass;
    }

    public function getFrequentlyBoughtTogether($product, string $index, int $max = 10): iterable
    {
        if ($product instanceof ObjectIdAwareInterface) {
            $product = $product->getObjectId();
        }
        Assert::scalar($product);

        $response = $this->recommendClient->getFrequentlyBoughtTogether([
            RecommendationRequest::createFrequentlyBoughtTogether($index, (string) $product, $max)->toArray(),
        ]);

        Assert::keyExists($response, 'results');
        Assert::isArray($response['results']);

        foreach ($response['results'] as $result) {
            Assert::isArray($result);
            Assert::keyExists($result, 'hits');
            Assert::isArray($result['hits']);

            /** @var mixed $hit */
            foreach ($result['hits'] as $hit) {
                yield $this->denormalizer->denormalize($hit, $this->productDocumentClass, 'json');
            }
        }
    }
}
