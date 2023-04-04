<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Handler;

use Setono\SyliusAlgoliaPlugin\Config\IndexRegistry;
use Setono\SyliusAlgoliaPlugin\Exception\NonExistingIndexException;
use Setono\SyliusAlgoliaPlugin\Exception\NonExistingResourceException;
use Setono\SyliusAlgoliaPlugin\Message\Command\IndexResource;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class IndexResourceHandler implements MessageHandlerInterface
{
    private IndexRegistry $indexRegistry;

    public function __construct(IndexRegistry $indexRegistry)
    {
        $this->indexRegistry = $indexRegistry;
    }

    public function __invoke(IndexResource $message): void
    {
        try {
            $this->indexRegistry
                ->getByName($message->index)
                ->indexer
                ->indexResource($message->index, $message->resource)
            ;
        } catch (NonExistingResourceException|NonExistingIndexException $e) {
            throw new UnrecoverableMessageHandlingException($e->getMessage(), 0, $e);
        }
    }
}
