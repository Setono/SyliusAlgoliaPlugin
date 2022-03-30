<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\IndexNameResolver;

use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Registry\SupportsResourceAwareTrait;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Webmozart\Assert\Assert;

final class ProductIndexNameResolver implements IndexNameResolverInterface
{
    use SupportsResourceAwareTrait;

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

    /**
     * Will resolve an index name from the current application context, i.e. channel context, locale context etc
     */
    public function resolve($resource): string
    {
        $channel = $this->channelContext->getChannel();
        $channelCode = $channel->getCode();
        Assert::notNull($channelCode);

        $localeCode = $this->localeContext->getLocaleCode();
        $currencyCode = $this->currencyContext->getCurrencyCode();

        return sprintf(
            'products__%s__%s___%s',
            strtolower($channelCode),
            strtolower($localeCode),
            strtolower($currencyCode)
        );
    }

    public function resolveFromIndexScope(IndexScope $indexScope, $resource): string
    {
        return rtrim(sprintf('products__%s', (string) $indexScope), '_');
    }

    protected function getSupportingType(): string
    {
        return ProductInterface::class;
    }
}
