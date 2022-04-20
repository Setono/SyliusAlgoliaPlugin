<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Filter\Object;

use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Sylius\Component\Resource\Model\ResourceInterface;

interface FilterInterface
{
    /**
     * Works like array_filter. If the method returns true, the $entity will be in the resulting set of entities to index
     */
    public function filter(ResourceInterface $entity, Document $document, IndexScope $indexScope): bool;
}
