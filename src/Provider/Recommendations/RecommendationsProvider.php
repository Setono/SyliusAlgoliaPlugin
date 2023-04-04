<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\Recommendations;

use Doctrine\Persistence\ManagerRegistry;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusAlgoliaPlugin\Client\RecommendationsClient\RecommendationsClientInterface;
use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\Model\IndexableInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

final class RecommendationsProvider implements RecommendationsProviderInterface
{
    use ORMManagerTrait;

    private RecommendationsClientInterface $recommendationsClient;

    /** @var array<string, array{classes: array{model: class-string}}> */
    private array $resources;

    /**
     * @param array<string, array{classes: array{model: class-string}}> $resources
     */
    public function __construct(
        RecommendationsClientInterface $recommendationsClient,
        ManagerRegistry $managerRegistry,
        array $resources
    ) {
        $this->recommendationsClient = $recommendationsClient;
        $this->managerRegistry = $managerRegistry;
        $this->resources = $resources;
    }

    public function getFrequentlyBoughtTogether(ProductInterface $product, string $index, int $max = 10): \Generator
    {
        Assert::isInstanceOf($product, IndexableInterface::class);

        return $this->getRecommendations($this->recommendationsClient->getFrequentlyBoughtTogether($product, $index, $max));
    }

    public function getRelatedProducts(ProductInterface $product, string $index, int $max = 10): \Generator
    {
        Assert::isInstanceOf($product, IndexableInterface::class);

        return $this->getRecommendations($this->recommendationsClient->getRelatedProducts($product, $index, $max));
    }

    /**
     * @param iterable<Document> $documents
     *
     * @return \Generator<int, ProductInterface>
     */
    private function getRecommendations(iterable $documents): \Generator
    {
        foreach ($documents as $document) {
            Assert::notNull($document->resourceName);

            /** @var class-string|null $resourceClass */
            $resourceClass = $this->resources[$document->resourceName]['classes']['model'] ?? null;
            Assert::notNull($resourceClass);

            $repository = $this->getRepository($resourceClass);
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
