<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusAlgoliaPlugin\Provider\Recommendations;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusAlgoliaPlugin\Client\RecommendationsClient\RecommendationsClientInterface;
use Setono\SyliusAlgoliaPlugin\Document\Product as ProductDocument;
use Setono\SyliusAlgoliaPlugin\Provider\Recommendations\RecommendationsProvider;
use Tests\Setono\SyliusAlgoliaPlugin\Stubs\Entity\Product;

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

        $repository = $this->prophesize(ObjectRepository::class);
        $repository->findOneBy([
            'code' => 'product1',
        ])->willReturn($recommendedProduct);

        $entityManager = $this->prophesize(EntityManagerInterface::class);
        $entityManager->getRepository(Product::class)->willReturn($repository);

        $managerRegistry = $this->prophesize(ManagerRegistry::class);
        $managerRegistry->getManagerForClass(Product::class)->willReturn($entityManager);

        $provider = new RecommendationsProvider(new RecommendationsClient(), $managerRegistry->reveal(), [
            'sylius.product' => [
                'classes' => [
                    'model' => Product::class,
                ],
            ],
        ]);

        $recommendedProducts = iterator_to_array($provider->getFrequentlyBoughtTogether($product, 'index'));
        self::assertCount(1, $recommendedProducts);
        self::assertSame($recommendedProduct, $recommendedProducts[0]);
    }
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
