<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Filter\Object;

use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Model\FilterableInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

final class FilterableFilter implements FilterInterface
{
    public function filter(ResourceInterface $entity, Document $document, IndexScope $indexScope): bool
    {
        return $entity instanceof FilterableInterface ? $entity->filter($indexScope) : true;
    }
}
