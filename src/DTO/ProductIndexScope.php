<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DTO;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Webmozart\Assert\Assert;

final class ProductIndexScope
{
    /** @psalm-readonly */
    public string $channelCode;

    /** @psalm-readonly */
    public string $localeCode;

    /** @psalm-readonly */
    public string $currencyCode;

    /** @var array<string, mixed> */
    public array $data;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(string $channelCode, string $localeCode, string $currencyCode, array $data = [])
    {
        Assert::true('' !== $channelCode, 'The channel cannot be an empty string');
        Assert::true('' !== $localeCode, 'The locale cannot be an empty string');
        Assert::true('' !== $currencyCode, 'The currency cannot be an empty string');

        $this->channelCode = $channelCode;
        $this->localeCode = $localeCode;
        $this->currencyCode = $currencyCode;
        $this->data = $data;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function createFromObjects(
        ChannelInterface $channel,
        LocaleInterface $locale,
        CurrencyInterface $currency,
        array $data = []
    ): self {
        return new self((string) $channel->getCode(), (string) $locale->getCode(), (string) $currency->getCode(), $data);
    }
}
