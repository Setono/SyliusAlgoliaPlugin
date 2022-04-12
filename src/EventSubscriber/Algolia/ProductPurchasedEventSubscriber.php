<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\EventSubscriber\Algolia;

use Setono\SyliusAlgoliaPlugin\Message\Command\SendOrderEvent;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class ProductPurchasedEventSubscriber implements EventSubscriberInterface
{
    private MessageBusInterface $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
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

        $this->commandBus->dispatch(new SendOrderEvent($order));
    }
}
