<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\IndexScope;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\IndexScope\ProductIndexScope;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class ProductIndexScopeProvider implements IndexScopeProviderInterface
{
    private ChannelRepositoryInterface $channelRepository;

    public function __construct(ChannelRepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    public function getIndexScopes(): iterable
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

    public function supports($resource): bool
    {
        $class = $resource instanceof IndexableResource ? $resource->className : get_class($resource);

        return is_a($class, ProductInterface::class, true);
    }
}
