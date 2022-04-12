<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Command;

use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

/**
 * Will send a conversion event with the given order to Algolia
 */
final class SendOrderEvent implements CommandInterface
{
    public int $orderId;

    /**
     * @param OrderInterface|int $order
     */
    public function __construct($order)
    {
        if ($order instanceof OrderInterface) {
            $order = (int) $order->getId();
        }
        Assert::integer($order);

        $this->orderId = $order;
    }
}
