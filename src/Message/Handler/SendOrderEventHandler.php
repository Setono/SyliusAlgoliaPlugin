<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Handler;

use Setono\SyliusAlgoliaPlugin\Client\InsightsClient\InsightsClientInterface;
use Setono\SyliusAlgoliaPlugin\Message\Command\SendOrderEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendOrderEventHandler implements MessageHandlerInterface
{
    private InsightsClientInterface $insightsClient;

    public function __construct(InsightsClientInterface $insightsClient)
    {
        $this->insightsClient = $insightsClient;
    }

    public function __invoke(SendOrderEvent $message): void
    {
        $this->insightsClient->sendConversionEventFromOrder($message->order);
    }
}
