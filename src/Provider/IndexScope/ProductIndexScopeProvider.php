<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\IndexScope;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
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

    public function getAll(IndexableResource $indexableResource): iterable
    {
        /** @var ChannelInterface[] $channels */
        $channels = $this->channelRepository->findAll();

        foreach ($channels as $channel) {
            foreach ($channel->getLocales() as $locale) {
                foreach ($channel->getCurrencies() as $currency) {
                    yield new IndexScope(
                        $indexableResource,
                        $channel->getCode(),
                        $locale->getCode(),
                        $currency->getCode()
                    );
                }
            }
        }
    }

    public function getFromContext(IndexableResource $indexableResource): IndexScope
    {
        return $this->getFromChannelAndLocaleAndCurrency(
            $indexableResource,
            $this->channelContext->getChannel()->getCode(),
            $this->localeContext->getLocaleCode(),
            $this->currencyContext->getCurrencyCode()
        );
    }

    public function getFromChannelAndLocaleAndCurrency(
        IndexableResource $indexableResource,
        string $channelCode = null,
        string $localeCode = null,
        string $currencyCode = null
    ): IndexScope {
        return new IndexScope($indexableResource, $channelCode, $localeCode, $currencyCode);
    }

    public function supports(IndexableResource $indexableResource): bool
    {
        return is_a($indexableResource->className, ProductInterface::class, true);
    }
}
