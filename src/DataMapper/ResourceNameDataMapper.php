<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResourceCollection;
use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Sylius\Component\Resource\Model\ResourceInterface;

final class ResourceNameDataMapper extends AllSupportingDataMapper
{
    private IndexableResourceCollection $indexableResourceCollection;

    public function __construct(IndexableResourceCollection $indexableResourceCollection)
    {
        $this->indexableResourceCollection = $indexableResourceCollection;
    }

    public function map(ResourceInterface $source, Document $target, IndexScope $indexScope, array $context = []): void
    {
        $target->resourceName = $this->indexableResourceCollection->getByClass($source)->name;
    }
}
