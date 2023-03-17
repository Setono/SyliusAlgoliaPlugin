<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResourceRegistry;
use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Sylius\Component\Resource\Model\ResourceInterface;

final class ResourceNameDataMapper implements DataMapperInterface
{
    private IndexableResourceRegistry $indexableResourceRegistry;

    public function __construct(IndexableResourceRegistry $indexableResourceRegistry)
    {
        $this->indexableResourceRegistry = $indexableResourceRegistry;
    }

    public function map(ResourceInterface $source, Document $target, IndexScope $indexScope, array $context = []): void
    {
        $target->resourceName = $this->indexableResourceRegistry->getByClass($source)->name;
    }

    public function supports(
        ResourceInterface $source,
        Document $target,
        IndexScope $indexScope,
        array $context = []
    ): bool {
        return true;
    }
}
