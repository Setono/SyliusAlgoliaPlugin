<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\EventSubscriber\Algolia;

use Setono\SyliusAlgoliaPlugin\Message\Event\Algolia\OrderPlaced;
use Setono\SyliusAlgoliaPlugin\Provider\EventContext\EventContextProviderInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class OrderPlacedEventSubscriber implements EventSubscriberInterface
{
    private MessageBusInterface $commandBus;

    private EventContextProviderInterface $eventContextProvider;

    public function __construct(MessageBusInterface $commandBus, EventContextProviderInterface $eventContextProvider)
    {
        $this->commandBus = $commandBus;
        $this->eventContextProvider = $eventContextProvider;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.order.post_complete' => 'dispatch',
        ];
    }

    public function dispatch(ResourceControllerEvent $event): void
    {
        $order = $event->getSubject();
        Assert::isInstanceOf($order, OrderInterface::class);

        $this->commandBus->dispatch(new OrderPlaced($this->eventContextProvider->getEventContext(), $order));
    }
}
