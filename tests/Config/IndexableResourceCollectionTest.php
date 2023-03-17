<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusAlgoliaPlugin\Config;

use PHPUnit\Framework\TestCase;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResourceCollection;
use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\Document\Product as ProductDocument;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\Image;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\Taxon;

/**
 * @covers \Setono\SyliusAlgoliaPlugin\Config\IndexableResourceCollection
 */
final class IndexableResourceCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function it_adds_on_instantiation(): void
    {
        $indexableResource1 = new IndexableResource('sylius.product', Product::class, ProductDocument::class);
        $indexableResource2 = new IndexableResource('sylius.taxon', Taxon::class, TaxonDocument::class);

        $collection = new IndexableResourceCollection($indexableResource1, $indexableResource2);
        $resources = iterator_to_array($collection->getIterator());
        self::assertCount(2, $resources);

        self::assertSame($indexableResource1, $resources['sylius.product']);
        self::assertSame($indexableResource2, $resources['sylius.taxon']);
    }

    /**
     * @test
     */
    public function it_returns_true_when_collection_has_with_class(): void
    {
        $collection = $this->getCollection();
        self::assertTrue($collection->hasWithClass(Product::class));
        self::assertTrue($collection->hasWithClass(new Product()));
    }

    /**
     * @test
     */
    public function it_returns_false_when_collection_does_not_has_with_class(): void
    {
        $collection = $this->getCollection();
        self::assertFalse($collection->hasWithClass(Image::class));
    }

    /**
     * @test
     */
    public function it_returns_true_when_collection_has_with_name(): void
    {
        $collection = $this->getCollection();
        self::assertTrue($collection->hasWithName('sylius.product'));
    }

    /**
     * @test
     */
    public function it_returns_false_when_collection_does_not_has_with_name(): void
    {
        $collection = $this->getCollection();
        self::assertFalse($collection->hasWithName('sylius.image'));
    }

    /**
     * @test
     */
    public function it_gets_by_name(): void
    {
        $collection = $this->getCollection();
        $indexableResource = $collection->getByName('sylius.product');
        self::assertSame('sylius.product', $indexableResource->name);
    }

    /**
     * @test
     */
    public function it_gets_by_class(): void
    {
        $collection = $this->getCollection();
        $indexableResource = $collection->getByClass(Product::class);
        self::assertSame('sylius.product', $indexableResource->name);
    }

    /**
     * @test
     */
    public function it_gets_by_class2(): void
    {
        $collection = $this->getCollection();
        $indexableResource = $collection->getByClass(ChildChannel::class);
        self::assertSame('sylius.channel', $indexableResource->name);
    }

    /**
     * @test
     */
    public function it_gets_by_interface(): void
    {
        $collection = $this->getCollection();
        $indexableResource = $collection->getByClass(ProductInterface::class);
        self::assertSame('sylius.product', $indexableResource->name);
    }

    private function getCollection(): IndexableResourceCollection
    {
        return new IndexableResourceCollection(
            new IndexableResource('sylius.product', Product::class, ProductDocument::class),
            new IndexableResource('sylius.taxon', Taxon::class, TaxonDocument::class),
            new IndexableResource('sylius.channel', Channel::class, ChannelDocument::class)
        );
    }
}

class ChildChannel extends Channel
{
}

class TaxonDocument extends Document
{
}

class ChannelDocument extends Document
{
}
