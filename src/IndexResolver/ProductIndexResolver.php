<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\IndexResolver;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Webmozart\Assert\Assert;

final class ProductIndexResolver implements ProductIndexResolverInterface
{
    private ChannelContextInterface $channelContext;

    private LocaleContextInterface $localeContext;

    public function __construct(ChannelContextInterface $channelContext, LocaleContextInterface $localeContext)
    {
        $this->channelContext = $channelContext;
        $this->localeContext = $localeContext;
    }

    public function resolve(ChannelInterface $channel = null, string $localeCode = null): string
    {
        if (null === $channel) {
            $channel = $this->channelContext->getChannel();
        }

        $channelCode = $channel->getCode();
        Assert::notNull($channelCode);

        if (null === $localeCode) {
            $localeCode = $this->localeContext->getLocaleCode();
        }

        return sprintf('products__%s__%s', strtolower($channelCode), strtolower($localeCode));
    }
}
