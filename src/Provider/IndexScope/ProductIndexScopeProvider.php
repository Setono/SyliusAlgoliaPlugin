<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\IndexScope;

use Setono\SyliusAlgoliaPlugin\Config\Index;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

final class ProductIndexScopeProvider implements IndexScopeProviderInterface
{
    private ChannelContextInterface $channelContext;

    private LocaleContextInterface $localeContext;

    private CurrencyContextInterface $currencyContext;

    private ChannelRepositoryInterface $channelRepository;

    public function __construct(
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        CurrencyContextInterface $currencyContext,
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->channelContext = $channelContext;
        $this->localeContext = $localeContext;
        $this->currencyContext = $currencyContext;
        $this->channelRepository = $channelRepository;
    }

    public function getAll(Index $index): iterable
    {
        /** @var ChannelInterface[] $channels */
        $channels = $this->channelRepository->findAll();

        foreach ($channels as $channel) {
            foreach ($channel->getLocales() as $locale) {
                foreach ($channel->getCurrencies() as $currency) {
                    yield (new IndexScope($index))
                        ->withChannelCode($channel->getCode())
                        ->withLocaleCode($locale->getCode())
                        ->withCurrencyCode($currency->getCode())
                    ;
                }
            }
        }
    }

    public function getFromContext(Index $index): IndexScope
    {
        return $this->getFromChannelAndLocaleAndCurrency(
            $index,
            $this->channelContext->getChannel()->getCode(),
            $this->localeContext->getLocaleCode(),
            $this->currencyContext->getCurrencyCode()
        );
    }

    public function getFromChannelAndLocaleAndCurrency(
        Index $index,
        string $channelCode = null,
        string $localeCode = null,
        string $currencyCode = null
    ): IndexScope {
        return (new IndexScope($index))
            ->withChannelCode($channelCode)
            ->withLocaleCode($localeCode)
            ->withCurrencyCode($currencyCode)
        ;
    }

    public function supports(Index $index): bool
    {
        return $index->hasResourceWithClass(ProductInterface::class);
    }
}
