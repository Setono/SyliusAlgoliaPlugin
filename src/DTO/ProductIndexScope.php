<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DTO;

use Psl;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

final class ProductIndexScope
{
    public string $channel;

    public string $locale;

    public string $currency;

    /** @var array<string, mixed> */
    public array $data;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(string $channel, string $locale, string $currency, array $data = [])
    {
        Psl\invariant('' !== $channel, 'The channel cannot be an empty string');
        Psl\invariant('' !== $locale, 'The locale cannot be an empty string');
        Psl\invariant('' !== $currency, 'The currency cannot be an empty string');

        $this->channel = $channel;
        $this->locale = $locale;
        $this->currency = $currency;
        $this->data = $data;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function createFromObjects(ChannelInterface $channel, LocaleInterface $locale, CurrencyInterface $currency, array $data = []): self
    {
        return new self((string) $channel->getCode(), (string) $locale->getCode(), (string) $currency->getCode(), $data);
    }
}
