<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\IndexScope;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;

/**
 * NOT final - this makes it easier for plugin users to override this class and provide their own scope for an index
 */
class IndexScope
{
    /**
     * The resource that this scope applies to
     */
    public IndexableResource $resource;

    public ?string $channelCode;

    public ?string $localeCode;

    public ?string $currencyCode;

    public function __construct(IndexableResource $resource, string $channelCode = null, string $localeCode = null, string $currencyCode = null)
    {
        $this->resource = $resource;
        $this->channelCode = $channelCode;
        $this->localeCode = $localeCode;
        $this->currencyCode = $currencyCode;
    }
}
