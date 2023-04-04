<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusAlgoliaPlugin\Client\InsightsClient;

use Setono\SyliusAlgoliaPlugin\Client\InsightsClient\EventContext;
use Setono\SyliusAlgoliaPlugin\Client\InsightsClient\InsightsClient;
use Setono\SyliusAlgoliaPlugin\Config\Index;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\Config\IndexRegistry;
use Setono\SyliusAlgoliaPlugin\Document\Product as ProductDocument;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Provider\IndexScope\IndexScopeProviderInterface;
use Setono\SyliusAlgoliaPlugin\Resolver\IndexName\IndexNameResolverInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\ProductVariant;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Tests\Setono\SyliusAlgoliaPlugin\Client\AbstractClientTestCase;
use Tests\Setono\SyliusAlgoliaPlugin\Stubs\Entity\Product;

/**
 * @covers \Setono\SyliusAlgoliaPlugin\Client\InsightsClient\InsightsClient
 */
final class InsightsClientTest extends AbstractClientTestCase
{
    /**
     * @test
     */
    public function it_sends_conversion_event_from_order(): void
    {
        if (!$this->isLive()) {
            $this->markTestSkipped('This test only works on live application atm.');
        }

        $product = new class() extends Product {
            public function getId(): int
            {
                return 1;
            }
        };
        $variant = new ProductVariant();
        $variant->setProduct($product);

        $item = new OrderItem();
        $item->setVariant($variant);
        $order = new Order();
        $order->addItem($item);

        $index = new Index('products', ProductDocument::class, [
            'sylius.product' => new IndexableResource('sylius.product', Product::class),
        ]);
        $indexRegistry = new IndexRegistry();
        $indexRegistry->add($index);

        $indexNameResolver = new class() implements IndexNameResolverInterface {
            public function resolve($resource): string
            {
                return 'products__fashion_web__en_us__usd';
            }

            public function resolveFromIndexScope(IndexScope $indexScope): string
            {
                return 'products__fashion_web__en_us__usd';
            }

            public function supports(Index $index): bool
            {
                return true;
            }
        };

        $indexScopeProvider = new class() implements IndexScopeProviderInterface {
            public function getAll(Index $index): iterable
            {
                return [$this->getFromContext($index)];
            }

            public function getFromContext(Index $index): IndexScope
            {
                return (new IndexScope($index))
                    ->withChannelCode('FASHION_WEB')
                    ->withLocaleCode('en_US')
                    ->withCurrencyCode('USD')
                ;
            }

            public function getFromChannelAndLocaleAndCurrency(
                Index $index,
                string $channelCode = null,
                string $localeCode = null,
                string $currencyCode = null
            ): IndexScope {
                return $this->getFromContext($index);
            }

            public function supports(Index $index): bool
            {
                return true;
            }
        };

        $normalizer = new class() implements NormalizerInterface {
            public function normalize($object, $format = null, array $context = []): array
            {
                return [
                ];
            }

            public function supportsNormalization($data, $format = null): bool
            {
                return true;
            }
        };

        $client = new InsightsClient(
            $this->algoliaInsightsClient,
            $indexRegistry,
            $indexScopeProvider,
            $indexNameResolver,
            $normalizer
        );
        $client->sendConversionEventFromOrder($order, new EventContext('client_id', 'FASHION_WEB', 'en_US', 'USD'));
    }
}
