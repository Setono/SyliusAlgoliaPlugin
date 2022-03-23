<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Command;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;

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
