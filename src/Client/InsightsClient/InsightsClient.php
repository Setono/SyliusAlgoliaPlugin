<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Client\InsightsClient;

use Algolia\AlgoliaSearch\InsightsClient as AlgoliaInsightsClient;
use Setono\SyliusAlgoliaPlugin\Config\IndexRegistry;
use Setono\SyliusAlgoliaPlugin\Model\IndexableInterface;
use Setono\SyliusAlgoliaPlugin\Provider\IndexScope\IndexScopeProviderInterface;
use Setono\SyliusAlgoliaPlugin\Resolver\IndexName\IndexNameResolverInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class InsightsClient implements InsightsClientInterface
{
    private AlgoliaInsightsClient $algoliaInsightsClient;

    private IndexRegistry $indexRegistry;

    private IndexScopeProviderInterface $indexScopeProvider;

    private IndexNameResolverInterface $indexNameResolver;

    private NormalizerInterface $normalizer;

    public function __construct(
        AlgoliaInsightsClient $algoliaInsightsClient,
        IndexRegistry $indexRegistry,
        IndexScopeProviderInterface $indexScopeProvider,
        IndexNameResolverInterface $indexNameResolver,
        NormalizerInterface $normalizer
    ) {
        $this->algoliaInsightsClient = $algoliaInsightsClient;
        $this->indexRegistry = $indexRegistry;
        $this->indexScopeProvider = $indexScopeProvider;
        $this->indexNameResolver = $indexNameResolver;
        $this->normalizer = $normalizer;
    }

    public function sendConversionEventFromOrder(OrderInterface $order, EventContext $eventContext): void
    {
        $objectIds = [];
        $product = null;
        foreach ($order->getItems() as $item) {
            /** @var IndexableInterface|null $product */
            $product = $item->getProduct();
            if (null === $product) {
                continue;
            }

            Assert::isInstanceOf($product, IndexableInterface::class);

            $objectIds[] = $product->getObjectId();
        }

        if (null === $product || [] === $objectIds) {
            return;
        }

        Assert::isInstanceOf($product, ResourceInterface::class);

        $index = $this->indexRegistry->getByResourceClass($product);

        $indexScope = $this->indexScopeProvider->getFromChannelAndLocaleAndCurrency(
            $index,
            $eventContext->channelCode,
            $eventContext->localeCode,
            $eventContext->currencyCode
        );

        $indexName = $this->indexNameResolver->resolveFromIndexScope($indexScope);

        $event = new Event(
            Event::EVENT_TYPE_CONVERSION,
            Event::EVENT_NAME_PRODUCT_PURCHASED,
            $indexName,
            $eventContext->clientId,
            $objectIds,
            $order->getCreatedAt() ?? $eventContext->timestamp,
            $eventContext->queryId
        );

        $this->sendEvent($event);
    }

    public function sendProductDetailPageViewedEventFromProduct(
        ProductInterface $product,
        EventContext $eventContext
    ): void {
        Assert::isInstanceOf($product, IndexableInterface::class);

        $index = $this->indexRegistry->getByResourceClass($product);

        $indexScope = $this->indexScopeProvider->getFromChannelAndLocaleAndCurrency(
            $index,
            $eventContext->channelCode,
            $eventContext->localeCode,
            $eventContext->currencyCode
        );

        $indexName = $this->indexNameResolver->resolveFromIndexScope($indexScope);

        $event = new Event(
            Event::EVENT_TYPE_VIEW,
            Event::EVENT_NAME_PRODUCT_DETAIL_PAGE_VIEWED,
            $indexName,
            $eventContext->clientId,
            [$product->getObjectId()],
            $eventContext->timestamp,
            $eventContext->queryId
        );

        $this->sendEvent($event);
    }

    public function sendEvent(Event $event): void
    {
        $this->sendEvents([$event]);
    }

    /**
     * @param list<Event> $events
     */
    public function sendEvents(array $events): void
    {
        $events = array_map(function (Event $event) {
            return $this->normalizer->normalize($event);
        }, $events);

        $this->algoliaInsightsClient->sendEvents($events);
    }
}
