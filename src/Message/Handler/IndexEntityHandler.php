<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Handler;

use Setono\SyliusAlgoliaPlugin\Indexer\IndexerInterface;
use Setono\SyliusAlgoliaPlugin\Message\Command\IndexEntity;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class IndexEntityHandler implements MessageHandlerInterface
{
    private IndexerInterface $indexer;

    public function __construct(IndexerInterface $indexer)
    {
        $this->indexer = $indexer;
    }

    public function __invoke(IndexEntity $message): void
    {
        try {
            $this->indexer->indexEntityWithId($message->entityId, $message->entityClass);
        } catch (\InvalidArgumentException $e) {
            // todo create better exception
            throw new UnrecoverableMessageHandlingException($e->getMessage(), 0, $e);
        }
    }
}
