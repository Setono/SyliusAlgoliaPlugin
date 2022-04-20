<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper;

use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Sylius\Component\Resource\Model\ResourceInterface;

final class IdDataMapper extends AllSupportingDataMapper
{
    public function map(ResourceInterface $source, Document $target, IndexScope $indexScope, array $context = []): void
    {
        $target->id = $source->getId();
    }
}
