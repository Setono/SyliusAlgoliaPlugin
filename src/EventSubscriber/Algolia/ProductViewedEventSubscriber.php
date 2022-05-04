<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\EventSubscriber\Algolia;

use Setono\SyliusAlgoliaPlugin\Message\Event\Algolia\ProductDetailPageViewed;
use Setono\SyliusAlgoliaPlugin\Provider\EventContext\EventContextProviderInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class ProductViewedEventSubscriber implements EventSubscriberInterface
{
    private MessageBusInterface $eventBus;

    private EventContextProviderInterface $eventContextProvider;

    public function __construct(
        MessageBusInterface $eventBus,
        EventContextProviderInterface $eventContextProvider
    ) {
        $this->eventBus = $eventBus;
        $this->eventContextProvider = $eventContextProvider;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.product.show' => 'dispatch',
        ];
    }

    public function dispatch(ResourceControllerEvent $event): void
    {
        $product = $event->getSubject();
        Assert::isInstanceOf($product, ProductInterface::class);

        $this->eventBus->dispatch(new ProductDetailPageViewed($this->eventContextProvider->getEventContext(), $product));
    }
}
