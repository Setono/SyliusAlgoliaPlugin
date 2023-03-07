<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper\Product;

use Setono\SyliusAlgoliaPlugin\DataMapper\DataMapperInterface;
use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\Document\Product;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

final class ProductDataMapper implements DataMapperInterface
{
    /**
     * @param ProductInterface|ResourceInterface $source
     * @param Product|Document $target
     * @param array<string, mixed> $context
     */
    public function map(ResourceInterface $source, Document $target, IndexScope $indexScope, array $context = []): void
    {
        Assert::true(
            $this->supports($source, $target, $indexScope, $context),
            'The given $source and $target is not supported'
        );

        $sourceTranslation = $source->getTranslation($indexScope->localeCode);

        $target->name = $sourceTranslation->getName();

        $createdAt = $source->getCreatedAt();
        if (null !== $createdAt) {
            $target->createdAt = $createdAt->getTimestamp();
        }
    }

    /**
     * @psalm-assert-if-true ProductInterface $source
     * @psalm-assert-if-true Product $target
     * @psalm-assert-if-true !null $indexScope->localeCode
     */
    public function supports(
        ResourceInterface $source,
        Document $target,
        IndexScope $indexScope,
        array $context = []
    ): bool {
        return $source instanceof ProductInterface
            && $target instanceof Product
            && null !== $indexScope->localeCode;
    }
}
