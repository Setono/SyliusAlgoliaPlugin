<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\EventSubscriber\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Setono\SyliusAlgoliaPlugin\Message\Command\IndexEntity;
use Setono\SyliusAlgoliaPlugin\Message\Command\RemoveEntity;
use Setono\SyliusAlgoliaPlugin\Model\IndexableInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class EntityChangeSubscriber implements EventSubscriber
{
    private MessageBusInterface $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist => 'update',
            Events::postUpdate => 'update',
            Events::postRemove => 'remove',
        ];
    }

    public function update(LifecycleEventArgs $eventArgs): void
    {
        $this->dispatch($eventArgs, static fn (IndexableInterface $entity) => new IndexEntity($entity));
    }

    public function remove(LifecycleEventArgs $eventArgs): void
    {
        $this->dispatch($eventArgs, static fn (IndexableInterface $entity) => new RemoveEntity($entity));
    }

    /**
     * @param callable(IndexableInterface):object $message
     */
    private function dispatch(LifecycleEventArgs $eventArgs, callable $message): void
    {
        $obj = $eventArgs->getObject();
        if (!$obj instanceof IndexableInterface) {
            return;
        }

        $this->commandBus->dispatch($message($obj));
    }
}
