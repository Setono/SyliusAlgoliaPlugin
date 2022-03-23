<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Handler;

use Setono\SyliusAlgoliaPlugin\Indexer\IndexerInterface;
use Setono\SyliusAlgoliaPlugin\Message\Command\IndexResource;
use Setono\SyliusAlgoliaPlugin\Registry\ResourceBasedRegistryInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class IndexResourceHandler implements MessageHandlerInterface
{
    /** @var ResourceBasedRegistryInterface<IndexerInterface> */
    private ResourceBasedRegistryInterface $indexerRegistry;

    public function __construct(ResourceBasedRegistryInterface $indexerRegistry)
    {
        $this->indexerRegistry = $indexerRegistry;
    }

    public function __invoke(IndexResource $message): void
    {
        try {
            /** @var IndexerInterface $indexer */
            $indexer = $this->indexerRegistry->get($message->resource);
        } catch (\InvalidArgumentException $e) {
            throw new UnrecoverableMessageHandlingException($e->getMessage(), 0, $e);
        }

        $indexer->indexResource($message->resource);
    }
}
