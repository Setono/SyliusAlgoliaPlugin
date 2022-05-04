<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Client\InsightsClient;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;

interface InsightsClientInterface
{
    public function sendConversionEventFromOrder(OrderInterface $order, EventContext $eventContext): void;

    public function sendProductDetailPageViewedEventFromProduct(ProductInterface $product, EventContext $eventContext): void;

    public function sendEvent(Event $event): void;

    /**
     * @param list<Event> $events
     */
    public function sendEvents(array $events): void;
}
