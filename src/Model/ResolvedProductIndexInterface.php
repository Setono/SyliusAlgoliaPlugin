<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Model;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

interface ResolvedProductIndexInterface
{
    public function getChannel(): ?ChannelInterface;

    public function setChannel(?ChannelInterface $channel): void;

    public function getChannelCode(): ?string;

    public function getLocale(): ?LocaleInterface;

    public function setLocale(?LocaleInterface $locale): void;

    public function getLocaleCode(): ?string;

    public function getCurrency(): ?CurrencyInterface;

    public function setCurrency(?CurrencyInterface $currency): void;

    public function getCurrencyCode(): ?string;
}
