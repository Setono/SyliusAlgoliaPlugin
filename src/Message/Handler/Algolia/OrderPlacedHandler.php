<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Handler\Algolia;

use Algolia\AlgoliaSearch\Exceptions\AlgoliaException;
use Algolia\AlgoliaSearch\Exceptions\BadRequestException;
use Setono\SyliusAlgoliaPlugin\Client\InsightsClient\InsightsClientInterface;
use Setono\SyliusAlgoliaPlugin\Message\Event\Algolia\OrderPlaced;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class OrderPlacedHandler implements MessageHandlerInterface
{
    private InsightsClientInterface $insightsClient;

    private OrderRepositoryInterface $orderRepository;

    public function __construct(InsightsClientInterface $insightsClient, OrderRepositoryInterface $orderRepository)
    {
        $this->insightsClient = $insightsClient;
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(OrderPlaced $message): void
    {
        $order = $this->orderRepository->find($message->orderId);
        if (null === $order) {
            throw new UnrecoverableMessageHandlingException(sprintf(
                'The order with id %s does not exist',
                (string) $message->orderId
            ));
        }

        if (!$order instanceof OrderInterface) {
            throw new UnrecoverableMessageHandlingException(sprintf(
                'Your order resource must implement the interface, %s',
                OrderInterface::class
            ));
        }

        try {
            $this->insightsClient->sendConversionEventFromOrder($order, $message->eventContext);
        } catch (AlgoliaException $e) {
            if ($e instanceof BadRequestException) {
                throw new UnrecoverableMessageHandlingException($e->getMessage(), 0, $e);
            }

            throw $e;
        }
    }
}
