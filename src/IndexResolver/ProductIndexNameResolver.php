<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\IndexResolver;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Webmozart\Assert\Assert;

final class ProductIndexNameResolver implements ProductIndexNameResolverInterface
{
    private ChannelContextInterface $channelContext;

    private LocaleContextInterface $localeContext;

    private CurrencyContextInterface $currencyContext;

    public function __construct(
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        CurrencyContextInterface $currencyContext
    ) {
        $this->channelContext = $channelContext;
        $this->localeContext = $localeContext;
        $this->currencyContext = $currencyContext;
    }

    public function resolve(string $channelCode = null, string $localeCode = null, string $currencyCode = null): string
    {
        if (null === $channelCode) {
            $channel = $this->channelContext->getChannel();
            $channelCode = $channel->getCode();
        }

        Assert::notNull($channelCode);

        if (null === $localeCode) {
            $localeCode = $this->localeContext->getLocaleCode();
        }

        if (null === $currencyCode) {
            $currencyCode = $this->currencyContext->getCurrencyCode();
        }

        return sprintf(
            'products__%s__%s___%s',
            strtolower($channelCode),
            strtolower($localeCode),
            strtolower($currencyCode)
        );
    }
}
