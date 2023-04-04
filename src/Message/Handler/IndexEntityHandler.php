<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Handler;

use Setono\SyliusAlgoliaPlugin\Config\IndexRegistry;
use Setono\SyliusAlgoliaPlugin\Message\Command\IndexEntity;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class IndexEntityHandler implements MessageHandlerInterface
{
    private IndexRegistry $indexRegistry;

    public function __construct(IndexRegistry $indexRegistry)
    {
        $this->indexRegistry = $indexRegistry;
    }

    public function __invoke(IndexEntity $message): void
    {
        try {
            $this->indexRegistry
                ->getByResourceClass($message->entityClass)
                ->indexer
                ->indexEntityWithId($message->entityId, $message->entityClass)
            ;
        } catch (\InvalidArgumentException $e) {
            // todo create better exception
            throw new UnrecoverableMessageHandlingException($e->getMessage(), 0, $e);
        }
    }
}
