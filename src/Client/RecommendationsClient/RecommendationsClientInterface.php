<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Client\RecommendationsClient;

use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\Model\IndexableInterface;

interface RecommendationsClientInterface
{
    /**
     * This method will return a list of documents that are frequently bought together with the given $product
     *
     * @param int|string|IndexableInterface $product
     *
     * @return iterable<Document>
     */
    public function getFrequentlyBoughtTogether($product, string $index, int $max = 10): iterable;

    /**
     * This method will return a list of documents that are related to the given $product
     *
     * @param int|string|IndexableInterface $product
     *
     * @return iterable<Document>
     */
    public function getRelatedProducts($product, string $index, int $max = 10): iterable;
}
