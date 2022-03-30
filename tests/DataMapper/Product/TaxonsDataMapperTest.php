<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusAlgoliaPlugin\DataMapper\Product;

use PHPUnit\Framework\TestCase;
use Setono\SyliusAlgoliaPlugin\DataMapper\Product\TaxonsDataMapper;
use Setono\SyliusAlgoliaPlugin\Document\Product as ProductDocument;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductTaxon;
use Sylius\Component\Core\Model\Taxon;
use Sylius\Component\Taxonomy\Model\TaxonTranslation;

/**
 * @covers \Setono\SyliusAlgoliaPlugin\DataMapper\Product\TaxonsDataMapper
 */
final class TaxonsDataMapperTest extends TestCase
{
    private const LOCALE = 'en_US';

    /**
     * @test
     */
    public function it_maps(): void
    {
        // create taxons
        $earlyWorkTaxon = $this->createProductTaxon('Early work');
        $shakespeareTaxon = $this->createProductTaxon('Shakespeare', $earlyWorkTaxon);
        $classicsTaxon = $this->createProductTaxon('Classics');
        $this->createProductTaxon('Books', $shakespeareTaxon, $classicsTaxon);

        $product = new Product();
        $product->addProductTaxon($classicsTaxon);
        $product->addProductTaxon($earlyWorkTaxon);

        $productDocument = new ProductDocument();

        $indexScope = new IndexScope();
        $indexScope->localeCode = self::LOCALE;

        $dataMapper = new TaxonsDataMapper();
        $dataMapper->map($product, $productDocument, $indexScope);

        self::assertEquals([
            'lvl0' => ['Books'],
            'lvl1' => ['Books > Classics', 'Books > Shakespeare'],
            'lvl2' => ['Books > Shakespeare > Early work'],
        ], $productDocument->taxons);
    }

    private function createProductTaxon(string $name, ProductTaxon ...$children): ProductTaxon
    {
        $taxon = new Taxon();
        $taxon->setCode(preg_replace('/[^a-z0-9_-]+/', '_', strtolower($name)));

        foreach ($children as $child) {
            /** @psalm-suppress PossiblyNullArgument */
            $taxon->addChild($child->getTaxon());
        }

        $translation = new TaxonTranslation();
        $translation->setName($name);
        $translation->setLocale(self::LOCALE);
        $taxon->addTranslation($translation);

        $productTaxon = new ProductTaxon();
        $productTaxon->setTaxon($taxon);

        return $productTaxon;
    }
}
