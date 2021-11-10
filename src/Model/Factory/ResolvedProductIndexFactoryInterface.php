<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Model\Factory;

use Setono\SyliusAlgoliaPlugin\Model\ResolvedProductIndexInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

interface ResolvedProductIndexFactoryInterface
{
    public function createNew(
        ?ChannelInterface $channel = null,
        ?LocaleInterface $locale = null,
        ?CurrencyInterface $currency = null
    ): ResolvedProductIndexInterface;
}
