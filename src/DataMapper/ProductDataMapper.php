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
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

final class ProductDataMapper implements DataMapperInterface
{
    use FormatAmountTrait;

    private ProductVariantResolverInterface $productVariantResolver;

    public function __construct(ProductVariantResolverInterface $productVariantResolver)
    {
        $this->productVariantResolver = $productVariantResolver;
    }

    /**
     * @param ProductInterface|ResourceInterface $source
     * @param Product|DocumentInterface $target
     */
    public function map(ResourceInterface $source, DocumentInterface $target, array $context = []): void
    {
        Psl\invariant($this->supports($source, $target), 'The given $source and $target is not supported');

        /** @var ProductVariantInterface|null $variant */
        $variant = $this->productVariantResolver->getVariant($source);

        $target->id = (int) $source->getId();
        $target->name = (string) $source->getName();

        foreach ($source->getImages() as $image) {
            $target->image = '/media/image/' . (string) $image->getPath();

            break;
        }

        $mainTaxon = $source->getMainTaxon();
        if (null !== $mainTaxon) {
            $target->taxons[] = (string) $mainTaxon->getName();
        }

        foreach ($source->getTaxons() as $taxon) {
            $target->taxons[] = (string) $taxon->getName();
        }

        if (isset($context['channel'])) {
            /** @var ChannelInterface|mixed $channel */
            $channel = $context['channel'];
            Assert::isInstanceOf($channel, ChannelInterface::class);

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
    }

    /**
     * @psalm-assert-if-true ProductInterface $source
     * @psalm-assert-if-true Product $target
     */
    public function supports(ResourceInterface $source, DocumentInterface $target, array $context = []): bool
    {
        return $source instanceof ProductInterface && $target instanceof Product;
    }
}
