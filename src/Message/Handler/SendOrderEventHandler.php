<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Handler;

use Algolia\AlgoliaSearch\Exceptions\AlgoliaException;
use Algolia\AlgoliaSearch\Exceptions\BadRequestException;
use Setono\SyliusAlgoliaPlugin\Client\InsightsClient\InsightsClientInterface;
use Setono\SyliusAlgoliaPlugin\Message\Command\SendOrderEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendOrderEventHandler implements MessageHandlerInterface
{
    private InsightsClientInterface $insightsClient;

    private OrderRepositoryInterface $orderRepository;

    public function __construct(InsightsClientInterface $insightsClient, OrderRepositoryInterface $orderRepository)
    {
        $this->insightsClient = $insightsClient;
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(SendOrderEvent $message): void
    {
        $order = $this->orderRepository->find($message->orderId);
        if (null === $order) {
            throw new UnrecoverableMessageHandlingException(sprintf('The order with id %d does not exist', $message->orderId));
        }

        if (!$order instanceof OrderInterface) {
            throw new UnrecoverableMessageHandlingException(sprintf('Your order resource must implement the interface, %s', OrderInterface::class));
        }

        try {
            $this->insightsClient->sendConversionEventFromOrder($order);
        } catch (AlgoliaException $e) {
            if ($e instanceof BadRequestException) {
                throw new UnrecoverableMessageHandlingException($e->getMessage(), 0, $e);
            }

            throw $e;
        }
    }
}
