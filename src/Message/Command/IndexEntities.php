<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Command;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;

final class IndexEntities implements CommandInterface
{
    public IndexableResource $resource;

    /** @var list<scalar> */
    public array $ids;

    /**
     * @param list<scalar> $ids
     */
    public function __construct(IndexableResource $resource, array $ids)
    {
        $this->resource = $resource;
        $this->ids = $ids;
    }
}
