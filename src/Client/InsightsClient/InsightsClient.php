<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Client\InsightsClient;

use Algolia\AlgoliaSearch\InsightsClient as AlgoliaInsightsClient;
use Setono\ClientId\Provider\ClientIdProviderInterface;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResourceCollection;
use Setono\SyliusAlgoliaPlugin\IndexNameResolver\IndexNameResolverInterface;
use Setono\SyliusAlgoliaPlugin\Model\ObjectIdAwareInterface;
use Setono\SyliusAlgoliaPlugin\Provider\IndexScope\IndexScopeProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

final class InsightsClient implements InsightsClientInterface
{
    private AlgoliaInsightsClient $algoliaInsightsClient;

    private ClientIdProviderInterface $clientIdProvider;

    private IndexableResourceCollection $indexableResourceCollection;

    private IndexScopeProviderInterface $indexScopeProvider;

    private IndexNameResolverInterface $indexNameResolver;

    public function __construct(
        AlgoliaInsightsClient $algoliaInsightsClient,
        ClientIdProviderInterface $clientIdProvider,
        IndexableResourceCollection $indexableResourceCollection,
        IndexScopeProviderInterface $indexScopeProvider,
        IndexNameResolverInterface $indexNameResolver
    ) {
        $this->algoliaInsightsClient = $algoliaInsightsClient;
        $this->clientIdProvider = $clientIdProvider;
        $this->indexableResourceCollection = $indexableResourceCollection;
        $this->indexScopeProvider = $indexScopeProvider;
        $this->indexNameResolver = $indexNameResolver;
    }

    public function sendConversionEventFromOrder(OrderInterface $order, string $queryId = null): void
    {
        $objectIds = [];
        $product = null;
        foreach ($order->getItems() as $item) {
            /** @var ObjectIdAwareInterface|null $product */
            $product = $item->getProduct();
            if (null === $product) {
                continue;
            }

            Assert::isInstanceOf($product, ObjectIdAwareInterface::class);

            $objectIds[] = $product->getObjectId();
        }

        if (null === $product || [] === $objectIds) {
            return;
        }

        Assert::isInstanceOf($product, ResourceInterface::class);

        $indexableResource = $this->indexableResourceCollection->getByClass($product);

        $channel = $order->getChannel();
        Assert::notNull($channel);

        $locale = $order->getLocaleCode();
        Assert::notNull($locale);

        $currencyCode = $order->getCurrencyCode();
        Assert::notNull($currencyCode);

        $indexScope = $this->indexScopeProvider->getFromChannelAndLocaleAndCurrency(
            $indexableResource,
            $channel->getCode(),
            $locale,
            $currencyCode
        );

        $indexName = $this->indexNameResolver->resolveFromIndexScope($indexScope);

        $event = new Event(
            Event::EVENT_TYPE_CONVERSION,
            Event::EVENT_NAME,
            $indexName,
            (string) $this->clientIdProvider->getClientId(),
            $objectIds
        );
        $event->queryId = $queryId;

        $createdAt = $order->getCreatedAt();
        if (null !== $createdAt) {
            $event->timestamp = (int) $createdAt->format('Uv');
        }

        $this->algoliaInsightsClient->sendEvent($event->toArray());
    }
}
