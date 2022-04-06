<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusAlgoliaPlugin\Client\InsightsClient;

use Setono\ClientId\ClientId;
use Setono\ClientId\Provider\ClientIdProviderInterface;
use Setono\SyliusAlgoliaPlugin\Client\InsightsClient\InsightsClient;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResourceCollection;
use Setono\SyliusAlgoliaPlugin\IndexNameResolver\IndexNameResolverInterface;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Model\ObjectIdAwareInterface;
use Setono\SyliusAlgoliaPlugin\Model\ObjectIdAwareTrait;
use Setono\SyliusAlgoliaPlugin\Provider\IndexScope\IndexScopeProviderInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductVariant;
use Tests\Setono\SyliusAlgoliaPlugin\Client\AbstractClientTestCase;

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

        $clientIdProvider = new class() implements ClientIdProviderInterface {
            public function getClientId(): ClientId
            {
                return new ClientId('client_id');
            }
        };

        $product = new class() extends Product implements ObjectIdAwareInterface {
            use ObjectIdAwareTrait;

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

        $indexableResource = new IndexableResource('sylius.product', Product::class);
        $indexableResourceCollection = new IndexableResourceCollection($indexableResource);

        $indexNameResolver = new class() implements IndexNameResolverInterface {
            public function resolve($resource): string
            {
                return 'products__fashion_web__en_us__usd';
            }

            public function resolveFromIndexScope(IndexScope $indexScope): string
            {
                return 'products__fashion_web__en_us__usd';
            }

            public function supports(IndexableResource $indexableResource): bool
            {
                return true;
            }
        };

        $indexScopeProvider = new class() implements IndexScopeProviderInterface {
            public function getAll(IndexableResource $indexableResource): iterable
            {
                return [$this->getFromContext($indexableResource)];
            }

            public function getFromContext(IndexableResource $indexableResource): IndexScope
            {
                return new IndexScope($indexableResource, 'FASHION_WEB', 'en_US', 'USD');
            }

            public function getFromChannelAndLocaleAndCurrency(
                IndexableResource $indexableResource,
                string $channelCode = null,
                string $localeCode = null,
                string $currencyCode = null
            ): IndexScope {
                return $this->getFromContext($indexableResource);
            }

            public function supports(IndexableResource $indexableResource): bool
            {
                return true;
            }
        };

        $client = new InsightsClient(
            $this->algoliaInsightsClient,
            $clientIdProvider,
            $indexableResourceCollection,
            $indexScopeProvider,
            $indexNameResolver
        );
        $client->sendConversionEventFromOrder($order);
    }
}
