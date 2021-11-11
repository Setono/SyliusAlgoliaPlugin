<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider;

use Setono\SyliusAlgoliaPlugin\DTO\ProductIndexScope;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class ProductIndexScopesProvider implements ProductIndexScopesProviderInterface
{
    private ChannelRepositoryInterface $channelRepository;

    public function __construct(ChannelRepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    public function getProductIndexScopes(): iterable
    {
        /** @var ChannelInterface[] $channels */
        $channels = $this->channelRepository->findAll();

        foreach ($channels as $channel) {
            foreach ($channel->getLocales() as $locale) {
                foreach ($channel->getCurrencies() as $currency) {
                    yield ProductIndexScope::createFromObjects($channel, $locale, $currency);
                }
            }
        }
    }
}
