<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Command;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;

/**
 * A resource in this command are all entities gathered under a resource name, i.e. if you sent this command with the
 * resource 'sylius.product' then it would index ALL product entities
 */
final class IndexResource implements CommandInterface
{
    /**
     * The resource that should be indexed in Algolia
     */
    public IndexableResource $resource;

    public function __construct(IndexableResource $resource)
    {
        $this->resource = $resource;
    }
}
