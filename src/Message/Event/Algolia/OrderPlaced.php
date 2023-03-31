<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Event\Algolia;

use Setono\SyliusAlgoliaPlugin\Client\InsightsClient\EventContext;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderPlaced extends BaseEvent
{
    /**
     * This is the order id
     *
     * @var string|int
     */
    public $order;

    /**
     * @param int|string|OrderInterface $order
     */
    public function __construct(EventContext $eventContext, $order)
    {
        parent::__construct($eventContext);

        if ($order instanceof OrderInterface) {
            /** @var mixed $order */
            $order = $order->getId();
        }

        if (!is_string($order) && !is_int($order)) {
            throw new \InvalidArgumentException(sprintf('The order must be an instance of int|string|%s', OrderInterface::class));
        }

        $this->order = $order;
    }
}
