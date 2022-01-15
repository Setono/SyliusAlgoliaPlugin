<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper;

use Setono\SyliusAlgoliaPlugin\Document\DocumentInterface;
use Setono\SyliusAlgoliaPlugin\Document\Product;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

/**
 * todo should probably be split into multiple services to make it easier to read and extend
 */
final class ProductDataMapper implements DataMapperInterface
{
    /**
     * @param ProductInterface|ResourceInterface $source
     * @param Product|DocumentInterface $target
     * @param array<string, mixed> $context
     */
    public function map(ResourceInterface $source, DocumentInterface $target, array $context = []): void
    {
        Assert::true($this->supports($source, $target, $context), 'The given $source and $target is not supported');

        $sourceTranslation = $source->getTranslation($context['locale']);

        $target->id = (int) $source->getId();
        $target->code = $source->getCode();
        $target->name = (string) $sourceTranslation->getName();

        $createdAt = $source->getCreatedAt();
        if (null !== $createdAt) {
            $target->createdAt = $createdAt->getTimestamp();
        }

        $this->mapOptions($source, $target, $context['locale']);
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

    /**
     * @psalm-assert-if-true ProductInterface $source
     * @psalm-assert-if-true Product $target
     * @psalm-assert-if-true string $context['locale']
     */
    public function supports(ResourceInterface $source, DocumentInterface $target, array $context = []): bool
    {
        return $source instanceof ProductInterface
            && $target instanceof Product
            && isset($context['locale'])
            && is_string($context['locale'])
        ;
    }
}
