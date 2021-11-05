<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper\Product;

use Psl;
use Setono\SyliusAlgoliaPlugin\DataMapper\DataMapperInterface;
use Setono\SyliusAlgoliaPlugin\Document\DocumentInterface;
use Setono\SyliusAlgoliaPlugin\Document\Product as ProductDocument;
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
     * @param ProductDocument|DocumentInterface $target
     * @param array<string, mixed> $context
     */
    public function map(ResourceInterface $source, DocumentInterface $target, array $context = []): void
    {
        Psl\invariant($this->supports($source, $target, $context), 'The given $source and $target is not supported');

        $mainTaxon = $source->getMainTaxon();
        if (null !== $mainTaxon) {
            $this->populateTaxons($target, $mainTaxon);
        }

        foreach ($source->getTaxons() as $taxon) {
            $this->populateTaxons($target, $taxon);
        }

        $target->taxonCodes = array_unique($target->taxonCodes);
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
    public function supports(ResourceInterface $source, DocumentInterface $target, array $context = []): bool
    {
        return $source instanceof ProductInterface && $target instanceof ProductDocument;
    }
}
