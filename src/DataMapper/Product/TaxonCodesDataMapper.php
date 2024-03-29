<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper\Product;

use Setono\SyliusAlgoliaPlugin\DataMapper\DataMapperInterface;
use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\Document\Product as ProductDocument;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Webmozart\Assert\Assert;

final class TaxonCodesDataMapper implements DataMapperInterface
{
    private bool $includeDescendants;

    public function __construct(bool $includeDescendants)
    {
        $this->includeDescendants = $includeDescendants;
    }

    /**
     * @param ProductInterface|ResourceInterface $source
     * @param ProductDocument|Document $target
     * @param array<string, mixed> $context
     */
    public function map(ResourceInterface $source, Document $target, IndexScope $indexScope, array $context = []): void
    {
        Assert::true($this->supports($source, $target, $indexScope, $context), 'The given $source and $target is not supported');

        $mainTaxon = $source->getMainTaxon();
        if (null !== $mainTaxon) {
            $this->populateTaxons($target, $mainTaxon);
        }

        foreach ($source->getTaxons() as $taxon) {
            $this->populateTaxons($target, $taxon);
        }

        $target->taxonCodes = array_values(array_unique($target->taxonCodes));
    }

    private function populateTaxons(ProductDocument $productDocument, TaxonInterface $taxon): void
    {
        $productDocument->taxonCodes[] = (string) $taxon->getCode();
        if ($this->includeDescendants) {
            foreach ($taxon->getAncestors() as $ancestor) {
                $productDocument->taxonCodes[] = (string) $ancestor->getCode();
            }
        }
    }

    /**
     * @psalm-assert-if-true ProductInterface $source
     * @psalm-assert-if-true ProductDocument $target
     */
    public function supports(ResourceInterface $source, Document $target, IndexScope $indexScope, array $context = []): bool
    {
        return $source instanceof ProductInterface && $target instanceof ProductDocument;
    }
}
