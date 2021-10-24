<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper;

use Setono\SyliusAlgoliaPlugin\Document\DocumentInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface DataMapperInterface
{
    /**
     * Maps $source to $target. This means properties on $target
     * are updated, but properties on $source remain untouched
     *
     * @param array<string, mixed> $context
     */
    public function map(ResourceInterface $source, DocumentInterface $target, array $context = []): void;

    /**
     * Returns true if this data mapper supports the given $source and $target
     *
     * @param array<string, mixed> $context
     */
    public function supports(ResourceInterface $source, DocumentInterface $target, array $context = []): bool;
}