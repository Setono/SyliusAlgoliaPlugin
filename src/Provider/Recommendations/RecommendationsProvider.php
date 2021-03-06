<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\Recommendations;

use Doctrine\Persistence\ManagerRegistry;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusAlgoliaPlugin\Client\RecommendationsClient\RecommendationsClientInterface;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResourceCollection;
use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\Model\ObjectIdAwareInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

final class RecommendationsProvider implements RecommendationsProviderInterface
{
    use ORMManagerTrait;

    private RecommendationsClientInterface $recommendationsClient;

    private IndexableResourceCollection $indexableResourceCollection;

    public function __construct(
        RecommendationsClientInterface $recommendationsClient,
        IndexableResourceCollection $indexableResourceCollection,
        ManagerRegistry $managerRegistry
    ) {
        $this->recommendationsClient = $recommendationsClient;
        $this->indexableResourceCollection = $indexableResourceCollection;
        $this->managerRegistry = $managerRegistry;
    }

    public function getFrequentlyBoughtTogether(ProductInterface $product, string $index, int $max = 10): \Generator
    {
        Assert::isInstanceOf($product, ObjectIdAwareInterface::class);

        return $this->getRecommendations($this->recommendationsClient->getFrequentlyBoughtTogether($product, $index, $max));
    }

    public function getRelatedProducts(ProductInterface $product, string $index, int $max = 10): \Generator
    {
        Assert::isInstanceOf($product, ObjectIdAwareInterface::class);

        return $this->getRecommendations($this->recommendationsClient->getRelatedProducts($product, $index, $max));
    }

    /**
     * @param iterable<Document> $documents
     *
     * @return \Generator<ProductInterface>
     */
    private function getRecommendations(iterable $documents): \Generator
    {
        foreach ($documents as $document) {
            Assert::notNull($document->resourceName);

            $indexableResource = $this->indexableResourceCollection->getByName($document->resourceName);

            $repository = $this->getRepository($indexableResource->className);
            $entity = $repository->findOneBy([
                'code' => $document->code,
            ]);

            Assert::nullOrIsInstanceOf($entity, ProductInterface::class);

            if (null !== $entity) {
                yield $entity;
            }
        }
    }
}
