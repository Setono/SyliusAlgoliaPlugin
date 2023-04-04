<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\IndexScope;

use Setono\SyliusAlgoliaPlugin\Config\Index;

/**
 * NOT final - this makes it easier for plugin users to override this class and provide their own scope for an index
 */
class IndexScope
{
    /**
     * The index that this scope applies to
     */
    public Index $index;

    public ?string $channelCode = null;

    public ?string $localeCode = null;

    public ?string $currencyCode = null;

    public function __construct(Index $index)
    {
        $this->index = $index;
    }

    /**
     * @return static
     */
    public function withChannelCode(?string $channelCode): self
    {
        $obj = clone $this;
        $obj->channelCode = $channelCode;

        return $obj;
    }

    /**
     * @return static
     */
    public function withLocaleCode(?string $localeCode): self
    {
        $obj = clone $this;
        $obj->localeCode = $localeCode;

        return $obj;
    }

    /**
     * @return static
     */
    public function withCurrencyCode(?string $currencyCode): self
    {
        $obj = clone $this;
        $obj->currencyCode = $currencyCode;

        return $obj;
    }
}
