<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper\Product;

use Setono\SyliusAlgoliaPlugin\DataMapper\DataMapperInterface;
use Setono\SyliusAlgoliaPlugin\Document\DocumentInterface;
use Setono\SyliusAlgoliaPlugin\Document\Product as ProductDocument;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Webmozart\Assert\Assert;

/**
 * todo the code in this class is so ugly, somebody please refactor it :D
 */
final class TaxonsDataMapper implements DataMapperInterface
{
    private const HIERARCHY_SEPARATOR = ' > ';

    /**
     * @param ProductInterface|ResourceInterface $source
     * @param ProductDocument|DocumentInterface $target
     * @param array<string, mixed> $context
     */
    public function map(ResourceInterface $source, DocumentInterface $target, IndexScope $indexScope, array $context = []): void
    {
        Assert::true($this->supports($source, $target, $indexScope, $context), 'The given $source and $target is not supported');

        $taxons = self::extractTaxons($source);

        if (count($taxons) === 0) {
            return;
        }

        /** @var array<array-key, string> $hierarchies */
        $hierarchies = [];
        foreach ($taxons as $taxon) {
            $hierarchies[] = self::buildHierarchy($taxon, $indexScope->localeCode);
        }

        $levels = [];

        foreach ($hierarchies as $hierarchy) {
            $levels = array_merge_recursive($levels, self::buildLevels($hierarchy));
        }

        foreach ($levels as &$level) {
            $level = array_unique($level);
        }
        unset($level);

        $target->taxons = $levels;
    }

    /**
     * @return array<string, array<array-key, string>>
     */
    private static function buildLevels(string $hierarchy): array
    {
        $levels = [];

        $numberOfLevelsZeroIndexed = count(explode(self::HIERARCHY_SEPARATOR, $hierarchy)) - 1;

        for ($i = $numberOfLevelsZeroIndexed; $i >= 0; --$i) {
            $levels['lvl' . $i] = [implode(
                self::HIERARCHY_SEPARATOR,
                explode(self::HIERARCHY_SEPARATOR, $hierarchy, $i - $numberOfLevelsZeroIndexed)
            )];
        }

        return $levels;
    }

    /**
     * Returns a string like 'Books > Shakespeare > Hamlet'
     */
    private static function buildHierarchy(TaxonInterface $taxon, string $locale): string
    {
        /** @var array<array-key, string> $ancestors */
        $ancestors = array_map(
            static function (TaxonInterface $taxon) use ($locale): string {
                return (string) $taxon->getTranslation($locale)->getName();
            },
            array_reverse($taxon->getAncestors()->toArray())
        );

        return implode(self::HIERARCHY_SEPARATOR, $ancestors) . self::HIERARCHY_SEPARATOR . (string) $taxon->getTranslation($locale)->getName();
    }

    /**
     * @return array<array-key, TaxonInterface>
     */
    private static function extractTaxons(ProductInterface $product): array
    {
        $taxons = [];

        $mainTaxon = $product->getMainTaxon();
        if (null !== $mainTaxon) {
            $taxons[(string) $mainTaxon->getCode()] = $mainTaxon;
        }

        foreach ($product->getTaxons() as $taxon) {
            $taxons[(string) $taxon->getCode()] = $taxon;
        }

        return $taxons;
    }

    /**
     * @psalm-assert-if-true ProductInterface $source
     * @psalm-assert-if-true ProductDocument $target
     * @psalm-assert-if-true !null $indexScope->localeCode
     */
    public function supports(ResourceInterface $source, DocumentInterface $target, IndexScope $indexScope, array $context = []): bool
    {
        return $source instanceof ProductInterface && $target instanceof ProductDocument && null !== $indexScope->localeCode;
    }
}
