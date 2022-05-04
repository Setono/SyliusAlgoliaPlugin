<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Event\Algolia;

use Setono\SyliusAlgoliaPlugin\Client\InsightsClient\EventContext;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderPlaced extends BaseEvent
{
    /** @var mixed */
    public $orderId;

    public function __construct(EventContext $eventContext, OrderInterface $order)
    {
        parent::__construct($eventContext);

        $this->orderId = $order->getId();
    }
}
