<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper;

use Psl;
use Setono\SyliusAlgoliaPlugin\Document\DocumentInterface;
use Setono\SyliusAlgoliaPlugin\Document\FormatAmountTrait;
use Setono\SyliusAlgoliaPlugin\Document\Product;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

/**
 * todo should probably be split into multiple services to make it easier to read and extend
 */
final class ProductDataMapper implements DataMapperInterface
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
     * @param Product|DocumentInterface $target
     * @param array<string, mixed> $context
     */
    public function map(ResourceInterface $source, DocumentInterface $target, array $context = []): void
    {
        Psl\invariant($this->supports($source, $target, $context), 'The given $source and $target is not supported');

        /** @var ChannelInterface $channel */
        $channel = $context['channel'];

        /** @var LocaleInterface $locale */
        $locale = $context['locale'];
        $localeCode = (string) $locale->getCode();

        $sourceTranslation = $source->getTranslation($localeCode);

        /** @var ProductVariantInterface|null $variant */
        $variant = $this->productVariantResolver->getVariant($source);

        $target->id = (int) $source->getId();
        $target->code = $source->getCode();
        $target->name = (string) $sourceTranslation->getName();

        // todo Sylius has the descendent configuration option. Should we use that to include all parent taxons here?
        $mainTaxon = $source->getMainTaxon();
        if (null !== $mainTaxon) {
            $target->taxonCodes[] = (string) $mainTaxon->getCode();
            $target->taxons[] = (string) $mainTaxon->getTranslation($localeCode)->getName();
        }

        foreach ($source->getTaxons() as $taxon) {
            $target->taxonCodes[] = (string) $taxon->getCode();
            $target->taxons[] = (string) $taxon->getTranslation($localeCode)->getName();
        }

        $target->taxonCodes = array_unique($target->taxonCodes);
        $target->taxons = array_unique($target->taxons);

        if (null !== $variant) {
            $this->mapPrices($target, $variant, $channel);
        }

        $this->mapOptions($source, $target, $localeCode);
    }

    private function mapOptions(ProductInterface $source, Product $target, string $locale): void
    {
        foreach ($source->getEnabledVariants() as $variant) {
            foreach ($variant->getOptionValues() as $optionValue) {
                $option = $optionValue->getOption();
                Assert::notNull($option);

                $optionName = $option->getTranslation($locale)->getName();
                Assert::notNull($optionName);

                $target->options[$optionName][] = (string) $optionValue->getTranslation($locale)->getValue();
            }
        }
    }

    private function mapPrices(Product $target, ProductVariantInterface $variant, ChannelInterface $channel): void
    {
        $baseCurrency = $channel->getBaseCurrency();
        Assert::notNull($baseCurrency);

        $baseCurrencyCode = $baseCurrency->getCode();
        Assert::notNull($baseCurrencyCode);

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
     * @psalm-assert-if-true Product $target
     * @psalm-assert-if-true ChannelInterface $context['channel']
     * @psalm-assert-if-true LocaleInterface $context['locale']
     */
    public function supports(ResourceInterface $source, DocumentInterface $target, array $context = []): bool
    {
        return $source instanceof ProductInterface
            && $target instanceof Product
            && isset($context['channel'], $context['locale'])
            && $context['channel'] instanceof ChannelInterface
            && $context['locale'] instanceof LocaleInterface
        ;
    }
}
