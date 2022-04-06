<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Command;

use Sylius\Component\Core\Model\OrderInterface;

/**
 * Will send a conversion event with the given order to Algolia
 */
final class SendOrderEvent implements CommandInterface
{
    public OrderInterface $order;

    public function __construct(OrderInterface $order)
    {
        $this->order = $order;
    }
}
