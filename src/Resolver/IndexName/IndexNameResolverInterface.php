<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Resolver\IndexName;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Sylius\Component\Resource\Model\ResourceInterface;

interface IndexNameResolverInterface
{
    /**
     * Will resolve an index name from the current application context, i.e. channel context, locale context etc
     *
     * @param class-string<ResourceInterface>|ResourceInterface|IndexableResource $resource
     */
    public function resolve($resource): string;

    public function resolveFromIndexScope(IndexScope $indexScope): string;

    public function supports(IndexableResource $indexableResource): bool;
}
