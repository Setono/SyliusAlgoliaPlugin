<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Client\RecommendationsClient;

use Algolia\AlgoliaSearch\RecommendClient;
use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\Model\IndexableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Webmozart\Assert\Assert;

final class RecommendationsClient implements RecommendationsClientInterface
{
    private RecommendClient $recommendClient;

    private DenormalizerInterface $denormalizer;

    public function __construct(
        RecommendClient $recommendClient,
        DenormalizerInterface $denormalizer
    ) {
        $this->recommendClient = $recommendClient;
        $this->denormalizer = $denormalizer;
    }

    public function getFrequentlyBoughtTogether($product, string $index, int $max = 10): iterable
    {
        if ($product instanceof IndexableInterface) {
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
                Assert::isArray($hit);
                Assert::keyExists($hit, 'documentClass');
                Assert::string($hit['documentClass']);

                $document = $this->denormalizer->denormalize($hit, $hit['documentClass'], 'json');
                Assert::isInstanceOf($document, Document::class);

                yield $document;
            }
        }
    }

    public function getRelatedProducts($product, string $index, int $max = 10): iterable
    {
        if ($product instanceof IndexableInterface) {
            $product = $product->getObjectId();
        }
        Assert::scalar($product);

        $response = $this->recommendClient->getRelatedProducts([
            RecommendationRequest::createRelatedProducts($index, (string) $product, $max)->toArray(),
        ]);

        Assert::keyExists($response, 'results');
        Assert::isArray($response['results']);

        foreach ($response['results'] as $result) {
            Assert::isArray($result);
            Assert::keyExists($result, 'hits');
            Assert::isArray($result['hits']);

            /** @var mixed $hit */
            foreach ($result['hits'] as $hit) {
                Assert::isArray($hit);
                Assert::keyExists($hit, 'documentClass');
                Assert::string($hit['documentClass']);

                $document = $this->denormalizer->denormalize($hit, $hit['documentClass'], 'json');
                Assert::isInstanceOf($document, Document::class);

                yield $document;
            }
        }
    }
}
