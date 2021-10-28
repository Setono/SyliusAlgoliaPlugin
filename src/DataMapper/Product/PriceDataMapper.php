<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper\Product;

use Psl;
use Setono\SyliusAlgoliaPlugin\DataMapper\DataMapperInterface;
use Setono\SyliusAlgoliaPlugin\Document\DocumentInterface;
use Setono\SyliusAlgoliaPlugin\Document\FormatAmountTrait;
use Setono\SyliusAlgoliaPlugin\Document\Product as ProductDocument;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

/**
 * This data mapper maps prices on product documents
 */
final class PriceDataMapper implements DataMapperInterface
{
    use FormatAmountTrait;

    private ProductVariantResolverInterface $productVariantResolver;

    private CurrencyConverterInterface $currencyConverter;

    public function __construct(ProductVariantResolverInterface $productVariantResolver, CurrencyConverterInterface $currencyConverter)
    {
        $this->productVariantResolver = $productVariantResolver;
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * @param ProductInterface|ResourceInterface $source
     * @param ProductDocument|DocumentInterface $target
     * @param array<string, mixed> $context
     */
    public function map(ResourceInterface $source, DocumentInterface $target, array $context = []): void
    {
        Psl\invariant($this->supports($source, $target, $context), 'The given $source and $target is not supported');

        /** @var ChannelInterface $channel */
        $channel = $context['channel'];

        $baseCurrency = $channel->getBaseCurrency();
        Assert::notNull($baseCurrency);

        $baseCurrencyCode = $baseCurrency->getCode();
        Assert::notNull($baseCurrencyCode);

        /** @var ProductVariantInterface|null $variant */
        $variant = $this->productVariantResolver->getVariant($source);
        if (null === $variant) {
            return;
        }

        $channelPricing = $variant->getChannelPricingForChannel($channel);
        if (null === $channelPricing) {
            return;
        }

        $price = $channelPricing->getPrice();
        if (null === $price) {
            return;
        }

        $target->currency = $baseCurrencyCode;
        $target->price = self::formatAmount($price);

        $originalPrice = $channelPricing->getOriginalPrice();
        if (null !== $originalPrice) {
            $target->originalPrice = self::formatAmount($originalPrice);
        }

        foreach ($channel->getCurrencies() as $currency) {
            $currencyCode = $currency->getCode();
            Assert::notNull($currencyCode);

            $target->prices[$currencyCode] = self::formatAmount($this->currencyConverter->convert(
                $price,
                $baseCurrencyCode,
                $currencyCode
            ));

            if (null !== $originalPrice) {
                $target->originalPrices[$currencyCode] = self::formatAmount($this->currencyConverter->convert(
                    $originalPrice,
                    $baseCurrencyCode,
                    $currencyCode
                ));
            }
        }
    }

    /**
     * @psalm-assert-if-true ProductInterface $source
     * @psalm-assert-if-true ProductDocument $target
     * @psalm-assert-if-true ChannelInterface $context['channel']
     */
    public function supports(ResourceInterface $source, DocumentInterface $target, array $context = []): bool
    {
        return $source instanceof ProductInterface
            && $target instanceof ProductDocument
            && isset($context['channel'])
            && $context['channel'] instanceof ChannelInterface
        ;
    }
}
