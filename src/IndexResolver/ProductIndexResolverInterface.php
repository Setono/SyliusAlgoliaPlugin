<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\IndexResolver;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

interface ProductIndexResolverInterface
{
    /**
     * Will return the product index name for the given channel and locale or if null is provided,
     * from the channel context and locale context respectively
     *
     * @param string|ChannelInterface|null $channel
     * @param string|LocaleInterface|null $locale
     */
    public function resolve($channel = null, $locale = null): string;
}
