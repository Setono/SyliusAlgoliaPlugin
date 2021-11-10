<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Model;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

class ResolvedProductIndex implements ResolvedProductIndexInterface
{
    public ?ChannelInterface $channel;

    public ?LocaleInterface $locale;

    public ?CurrencyInterface $currency;

    public function __construct(
        ?ChannelInterface $channel = null,
        ?LocaleInterface $locale = null,
        ?CurrencyInterface $currency = null
    ) {
        $this->channel = $channel;
        $this->locale = $locale;
        $this->currency = $currency;
    }

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(?ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }

    public function getChannelCode(): ?string
    {
        if (null !== $this->channel) {
            return $this->channel->getCode();
        }

        return null;
    }

    public function getLocale(): ?LocaleInterface
    {
        return $this->locale;
    }

    public function setLocale(?LocaleInterface $locale): void
    {
        $this->locale = $locale;
    }

    public function getLocaleCode(): ?string
    {
        if (null !== $this->locale) {
            return $this->locale->getCode();
        }

        return null;
    }

    public function getCurrency(): ?CurrencyInterface
    {
        return $this->currency;
    }

    public function setCurrency(?CurrencyInterface $currency): void
    {
        $this->currency = $currency;
    }

    public function getCurrencyCode(): ?string
    {
        if (null !== $this->currency) {
            return $this->currency->getCode();
        }

        return null;
    }
}
