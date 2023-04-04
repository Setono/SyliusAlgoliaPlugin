<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Handler;

use Setono\SyliusAlgoliaPlugin\Config\IndexRegistry;
use Setono\SyliusAlgoliaPlugin\Message\Command\RemoveEntity;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RemoveEntityHandler implements MessageHandlerInterface
{
    private IndexRegistry $indexRegistry;

    public function __construct(IndexRegistry $indexRegistry)
    {
        $this->indexRegistry = $indexRegistry;
    }

    public function __invoke(RemoveEntity $message): void
    {
        try {
            $this->indexRegistry
                ->getByResourceClass($message->entityClass)
                ->indexer
                ->removeEntityWithId($message->entityId, $message->entityClass)
            ;
        } catch (\InvalidArgumentException $e) {
            throw new UnrecoverableMessageHandlingException($e->getMessage(), 0, $e);
        }
    }
}
