<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\IndexNameResolver;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Registry\SupportsResourceAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface IndexNameResolverInterface extends SupportsResourceAwareInterface
{
    /**
     * Will resolve an index name from the current application context, i.e. channel context, locale context etc
     *
     * @param ResourceInterface|IndexableResource $resource
     */
    public function resolve($resource): string;

    /**
     * @param ResourceInterface|IndexableResource $resource
     */
    public function resolveFromIndexScope(IndexScope $indexScope, $resource): string;
}
