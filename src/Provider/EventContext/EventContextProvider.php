<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\EventContext;

use Setono\ClientId\Provider\ClientIdProviderInterface;
use Setono\SyliusAlgoliaPlugin\Client\InsightsClient\EventContext;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

final class EventContextProvider implements EventContextProviderInterface
{
    private ClientIdProviderInterface $clientIdProvider;

    private ChannelContextInterface $channelContext;

    private LocaleContextInterface $localeContext;

    private CurrencyContextInterface $currencyContext;

    public function __construct(
        ClientIdProviderInterface $clientIdProvider,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        CurrencyContextInterface $currencyContext
    ) {
        $this->clientIdProvider = $clientIdProvider;
        $this->channelContext = $channelContext;
        $this->localeContext = $localeContext;
        $this->currencyContext = $currencyContext;
    }

    public function getEventContext(): EventContext
    {
        return new EventContext(
            $this->clientIdProvider->getClientId(),
            $this->channelContext->getChannel(),
            $this->localeContext->getLocaleCode(),
            $this->currencyContext->getCurrencyCode()
        );
    }
}
