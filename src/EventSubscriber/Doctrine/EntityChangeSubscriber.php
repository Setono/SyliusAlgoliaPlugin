<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\EventSubscriber\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResourceRegistry;
use Setono\SyliusAlgoliaPlugin\Message\Command\IndexEntity;
use Setono\SyliusAlgoliaPlugin\Message\Command\RemoveEntity;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class EntityChangeSubscriber implements EventSubscriber
{
    private MessageBusInterface $commandBus;

    private IndexableResourceRegistry $indexableResourceRegistry;

    public function __construct(
        MessageBusInterface $commandBus,
        IndexableResourceRegistry $indexableResourceRegistry
    ) {
        $this->commandBus = $commandBus;
        $this->indexableResourceRegistry = $indexableResourceRegistry;
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
        $obj = $eventArgs->getObject();
        if (!$obj instanceof ResourceInterface) {
            return;
        }

        if (!$this->indexableResourceRegistry->hasWithClass($obj)) {
            return;
        }

        $this->commandBus->dispatch(new IndexEntity($obj));
    }

    public function remove(LifecycleEventArgs $eventArgs): void
    {
        $obj = $eventArgs->getObject();
        if (!$obj instanceof ResourceInterface) {
            return;
        }

        if (!$this->indexableResourceRegistry->hasWithClass($obj)) {
            return;
        }

        $this->commandBus->dispatch(new RemoveEntity($obj));
    }
}
