<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Command;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;

final class IndexEntities implements CommandInterface
{
    public IndexableResource $resource;

    /** @var non-empty-list<scalar> */
    public array $ids;

    /**
     * @param non-empty-list<scalar> $ids
     */
    public function __construct(IndexableResource $resource, array $ids)
    {
        $this->resource = $resource;
        $this->ids = $ids;
    }
}
