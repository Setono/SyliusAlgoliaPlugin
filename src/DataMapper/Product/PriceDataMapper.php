<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper\Product;

use Setono\SyliusAlgoliaPlugin\DataMapper\DataMapperInterface;
use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\Document\FormatAmountTrait;
use Setono\SyliusAlgoliaPlugin\Document\Product as ProductDocument;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

/**
 * This data mapper maps prices on product documents
 */
final class PriceDataMapper implements DataMapperInterface
{
    use FormatAmountTrait;

    private ChannelRepositoryInterface $channelRepository;

    public function __construct(ChannelRepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    /**
     * @param ProductInterface|ResourceInterface $source
     * @param ProductDocument|Document $target
     * @param array<string, mixed> $context
     */
    public function map(ResourceInterface $source, Document $target, IndexScope $indexScope, array $context = []): void
    {
        Assert::true($this->supports($source, $target, $indexScope, $context), 'The given $source and $target is not supported');

        /** @var BaseChannelInterface|ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($indexScope->channelCode);
        Assert::isInstanceOf($channel, ChannelInterface::class);

        $baseCurrency = $channel->getBaseCurrency();
        Assert::notNull($baseCurrency);

        $baseCurrencyCode = $baseCurrency->getCode();
        Assert::notNull($baseCurrencyCode);

        $price = null;
        $originalPrice = null;

        /**
         * Let's get the lowest price of any enabled variant and use that as our product price reference
         *
         * @var ProductVariantInterface $variant
         */
        foreach ($source->getEnabledVariants() as $variant) {
            $channelPricing = $variant->getChannelPricingForChannel($channel);
            if (null === $channelPricing) {
                continue;
            }

            if (null === $price || $channelPricing->getPrice() < $price) {
                $price = $channelPricing->getPrice();
                $originalPrice = $channelPricing->getOriginalPrice();
            }
        }

        // no variants have prices
        if (null === $price) {
            return;
        }

        $target->currency = $baseCurrencyCode;
        $target->price = self::formatAmount($price);

        if (null !== $originalPrice) {
            $target->originalPrice = self::formatAmount($originalPrice);
        }
    }

    /**
     * @psalm-assert-if-true ProductInterface $source
     * @psalm-assert-if-true ProductDocument $target
     * @psalm-assert-if-true !null $indexScope->channelCode
     */
    public function supports(ResourceInterface $source, Document $target, IndexScope $indexScope, array $context = []): bool
    {
        return $source instanceof ProductInterface
            && $target instanceof ProductDocument
            && $indexScope->channelCode !== null
        ;
    }
}
