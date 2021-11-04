<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\IndexResolver;

use Sylius\Component\Channel\Model\ChannelInterface;

interface ProductIndexResolverInterface
{
    /**
     * Will return the product index name for the given channel and locale or if null is provided,
     * from the channel context and locale context respectively
     */
    public function resolve(?ChannelInterface $channel = null, ?string $localeCode = null): string;
}
