<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Psl;
use Setono\SyliusAlgoliaPlugin\Document\DocumentInterface;
use Setono\SyliusAlgoliaPlugin\Document\FormatAmountTrait;
use Setono\SyliusAlgoliaPlugin\Document\Product;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

final class ProductDataMapper implements DataMapperInterface
{
    use FormatAmountTrait;

    private ProductVariantResolverInterface $productVariantResolver;

    private CacheManager $cacheManager;

    public function __construct(
        ProductVariantResolverInterface $productVariantResolver,
        CacheManager $cacheManager
    ) {
        $this->productVariantResolver = $productVariantResolver;
        $this->cacheManager = $cacheManager;
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
        $target->name = (string) $sourceTranslation->getName();

        // todo would probably make more sense to let the actual document generate their own image url by
        // todo implementing a method that takes the $source and the CacheManager as input
        foreach ($source->getImages() as $image) {
            $target->imageUrl = $this->cacheManager->getBrowserPath((string) $image->getPath(), 'sylius_shop_product_large_thumbnail'); // todo the filter should be configurable

            break;
        }

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

        $baseCurrency = $channel->getBaseCurrency();
        Assert::notNull($baseCurrency);

        if (null !== $variant) {
            $channelPricing = $variant->getChannelPricingForChannel($channel);
            if (null !== $channelPricing) {
                $target->baseCurrency = $baseCurrency->getCode();
                $target->basePrice = self::formatAmount($channelPricing->getPrice());
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
