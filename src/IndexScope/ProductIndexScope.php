<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\IndexScope;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

/**
 * This is the default product index scope for this plugin where we have an index for each channel, locale, and currency
 */
final class ProductIndexScope extends IndexScope
{
    public static function createFromObjects(
        ChannelInterface $channel,
        LocaleInterface $locale,
        CurrencyInterface $currency
    ): self {
        $obj = new self();
        $obj->channelCode = $channel->getCode();
        $obj->localeCode = $locale->getCode();
        $obj->currencyCode = $currency->getCode();

        return $obj;
    }
}
