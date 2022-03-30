<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\IndexScope;

/**
 * NOT final - this makes it easier for plugin users to override this class and provide their own scope for an index
 */
class IndexScope
{
    public ?string $channelCode = null;

    public ?string $localeCode = null;

    public ?string $currencyCode = null;

    public function __toString(): string
    {
        $str = '';

        if (null !== $this->channelCode) {
            $str .= $this->channelCode . '__';
        }

        if (null !== $this->localeCode) {
            $str .= $this->localeCode . '__';
        }

        if (null !== $this->currencyCode) {
            $str .= $this->currencyCode . '__';
        }

        return strtolower(rtrim($str, '_'));
    }
}
