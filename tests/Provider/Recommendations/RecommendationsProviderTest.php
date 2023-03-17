<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusAlgoliaPlugin\Provider\Recommendations;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusAlgoliaPlugin\Client\RecommendationsClient\RecommendationsClientInterface;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResourceCollection;
use Setono\SyliusAlgoliaPlugin\Document\Product as ProductDocument;
use Setono\SyliusAlgoliaPlugin\Model\ObjectIdAwareInterface;
use Setono\SyliusAlgoliaPlugin\Model\ObjectIdAwareTrait;
use Setono\SyliusAlgoliaPlugin\Provider\Recommendations\RecommendationsProvider;
use Sylius\Component\Core\Model\Product as BaseProduct;

/**
 * @covers \Setono\SyliusAlgoliaPlugin\Provider\Recommendations\RecommendationsProvider
 */
final class RecommendationsProviderTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_returns_frequently_bought_products(): void
    {
        $product = new Product();
        $recommendedProduct = new Product();
        $recommendedProduct->setCode('product1');

        $indexableResourceCollection = new IndexableResourceCollection(new IndexableResource('sylius.product', Product::class, ProductDocument::class));

        $repository = $this->prophesize(ObjectRepository::class);
        $repository->findOneBy([
            'code' => 'product1',
        ])->willReturn($recommendedProduct);

        $entityManager = $this->prophesize(EntityManagerInterface::class);
        $entityManager->getRepository(Product::class)->willReturn($repository);

        $managerRegistry = $this->prophesize(ManagerRegistry::class);
        $managerRegistry->getManagerForClass(Product::class)->willReturn($entityManager);

        $provider = new RecommendationsProvider(new RecommendationsClient(), $indexableResourceCollection, $managerRegistry->reveal());

        $recommendedProducts = iterator_to_array($provider->getFrequentlyBoughtTogether($product, 'index'));
        self::assertCount(1, $recommendedProducts);
        self::assertSame($recommendedProduct, $recommendedProducts[0]);
    }
}

final class Product extends BaseProduct implements ObjectIdAwareInterface
{
    use ObjectIdAwareTrait;
}

final class RecommendationsClient implements RecommendationsClientInterface
{
    public function getFrequentlyBoughtTogether($product, string $index, int $max = 10): iterable
    {
        $document = new ProductDocument();
        $document->resourceName = 'sylius.product';
        $document->code = 'product1';

        return [$document];
    }

    public function getRelatedProducts($product, string $index, int $max = 10): iterable
    {
        $document = new ProductDocument();
        $document->resourceName = 'sylius.product';
        $document->code = 'product1';

        return [$document];
    }
}
