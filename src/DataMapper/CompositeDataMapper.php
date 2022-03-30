<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper;

use Setono\SyliusAlgoliaPlugin\Document\DocumentInterface;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use SplPriorityQueue;
use Sylius\Component\Resource\Model\ResourceInterface;

final class CompositeDataMapper implements DataMapperInterface
{
    private SplPriorityQueue $dataMappers;

    public function __construct()
    {
        $this->dataMappers = new SplPriorityQueue();
    }

    public function add(DataMapperInterface $dataMapper, int $priority = 0): void
    {
        $this->dataMappers->insert($dataMapper, $priority);
    }

    public function map(ResourceInterface $source, DocumentInterface $target, IndexScope $indexScope, array $context = []): void
    {
        /** @var array<array-key, DataMapperInterface> $dataMappers */
        $dataMappers = clone $this->dataMappers; // todo use another implementation of a priority queue which does not dequeue on foreach

        foreach ($dataMappers as $dataMapper) {
            if ($dataMapper->supports($source, $target, $indexScope, $context)) {
                $dataMapper->map($source, $target, $indexScope, $context);
            }
        }
    }

    public function supports(ResourceInterface $source, DocumentInterface $target, IndexScope $indexScope, array $context = []): bool
    {
        /** @var array<array-key, DataMapperInterface> $dataMappers */
        $dataMappers = clone $this->dataMappers;

        foreach ($dataMappers as $dataMapper) {
            if ($dataMapper->supports($source, $target, $indexScope, $context)) {
                return true;
            }
        }

        return false;
    }
}
