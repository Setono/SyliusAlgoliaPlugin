<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Handler;

use Setono\SyliusAlgoliaPlugin\Indexer\IndexerInterface;
use Setono\SyliusAlgoliaPlugin\Message\Command\IndexResource;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class IndexResourceHandler implements MessageHandlerInterface
{
    private IndexerInterface $indexer;

    public function __construct(IndexerInterface $indexer)
    {
        $this->indexer = $indexer;
    }

    public function __invoke(IndexResource $message): void
    {
        try {
            $this->indexer->indexResource($message->resource);
        } catch (\InvalidArgumentException $e) {
            throw new UnrecoverableMessageHandlingException($e->getMessage(), 0, $e);
        }
    }
}
