<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\IndexResolver;

use Setono\SyliusAlgoliaPlugin\Model\Factory\ResolvedProductIndexFactoryInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class ProductsIndicesResolver implements ProductsIndicesResolverInterface
{
    private ResolvedProductIndexFactoryInterface $resolvedProductIndexFactory;

    private ChannelRepositoryInterface $channelRepository;

    public function __construct(ResolvedProductIndexFactoryInterface $resolvedProductIndexFactory, ChannelRepositoryInterface $channelRepository)
    {
        $this->resolvedProductIndexFactory = $resolvedProductIndexFactory;
        $this->channelRepository = $channelRepository;
    }

    public function resolve(): iterable
    {
        /** @var ChannelInterface[] $channels */
        $channels = $this->channelRepository->findAll();

        $indices = [];
        foreach ($channels as $channel) {
            foreach ($channel->getLocales() as $locale) {
                foreach ($channel->getCurrencies() as $currency) {
                    $indices[] = $this->resolvedProductIndexFactory->createNew($channel, $locale, $currency);
                }
            }
        }

        return $indices;
    }
}
