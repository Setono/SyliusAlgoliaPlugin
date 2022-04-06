<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Client\InsightsClient;

use Sylius\Component\Core\Model\OrderInterface;

interface InsightsClientInterface
{
    public function sendConversionEventFromOrder(OrderInterface $order, string $queryId = null): void;
}
