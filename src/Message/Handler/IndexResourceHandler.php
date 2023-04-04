<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Handler;

use Setono\SyliusAlgoliaPlugin\Config\IndexRegistry;
use Setono\SyliusAlgoliaPlugin\Exception\NonExistingIndexException;
use Setono\SyliusAlgoliaPlugin\Exception\NonExistingResourceException;
use Setono\SyliusAlgoliaPlugin\Indexer\IndexerInterface;
use Setono\SyliusAlgoliaPlugin\Message\Command\IndexResource;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class IndexResourceHandler implements MessageHandlerInterface
{
    private IndexRegistry $indexRegistry;

    private IndexerInterface $indexer;

    public function __construct(IndexRegistry $indexRegistry, IndexerInterface $indexer)
    {
        $this->indexRegistry = $indexRegistry;
        $this->indexer = $indexer;
    }

    public function __invoke(IndexResource $message): void
    {
        try {
            $index = $this->indexRegistry->getByName($message->index);
            $this->indexer->indexResource($index, $message->resource);
        } catch (NonExistingResourceException|NonExistingIndexException $e) {
            throw new UnrecoverableMessageHandlingException($e->getMessage(), 0, $e);
        }
    }
}
