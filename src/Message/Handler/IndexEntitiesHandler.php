<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Handler;

use Setono\SyliusAlgoliaPlugin\Config\IndexRegistry;
use Setono\SyliusAlgoliaPlugin\Message\Command\IndexEntities;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class IndexEntitiesHandler implements MessageHandlerInterface
{
    private IndexRegistry $indexRegistry;

    public function __construct(IndexRegistry $indexRegistry)
    {
        $this->indexRegistry = $indexRegistry;
    }

    public function __invoke(IndexEntities $message): void
    {
        try {
            $this->indexRegistry
                ->getByResourceClass($message->resource->class)
                ->indexer
                ->indexEntitiesWithIds($message->ids, $message->resource->class)
            ;
        } catch (\InvalidArgumentException $e) {
            throw new UnrecoverableMessageHandlingException($e->getMessage(), 0, $e);
        }
    }
}
