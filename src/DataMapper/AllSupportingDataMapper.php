<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper;

use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Sylius\Component\Resource\Model\ResourceInterface;

abstract class AllSupportingDataMapper implements DataMapperInterface
{
    public function supports(
        ResourceInterface $source,
        Document $target,
        IndexScope $indexScope,
        array $context = []
    ): bool {
        return true;
    }
}
