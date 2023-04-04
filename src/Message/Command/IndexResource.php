<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Command;

use Setono\SyliusAlgoliaPlugin\Config\Index;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;

/**
 * A resource in this command are all entities gathered under a resource name, i.e. if you send this command with the
 * resource 'sylius.product' then it would index ALL product entities
 */
final class IndexResource implements CommandInterface
{
    /**
     * The index where the resource should be indexed.
     */
    public string $index;

    /**
     * The resource that should be indexed in Algolia. The resource MUST be configured on the given index
     */
    public string $resource;

    /**
     * @param string|Index $index
     * @param string|IndexableResource $resource
     */
    public function __construct($index, $resource)
    {
        if ($index instanceof Index) {
            $index = $index->name;
        }

        if ($resource instanceof IndexableResource) {
            $resource = $resource->name;
        }

        $this->index = $index;
        $this->resource = $resource;
    }
}
