<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\IndexResolver;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
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

    public function resolve($channel = null, $locale = null): string
    {
        if (null === $channel) {
            $channel = $this->channelContext->getChannel();
        }

        if ($channel instanceof ChannelInterface) {
            $channel = $channel->getCode();
        }
        Assert::string($channel);

        if (null === $locale) {
            $locale = $this->localeContext->getLocaleCode();
        }
        if ($locale instanceof LocaleInterface) {
            $locale = $locale->getCode();
        }
        Assert::string($locale);

        return sprintf('products__%s__%s', $channel, $locale);
    }
}
