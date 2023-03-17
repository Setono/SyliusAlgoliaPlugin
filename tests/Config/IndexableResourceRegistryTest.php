<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusAlgoliaPlugin\Config;

use PHPUnit\Framework\TestCase;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResourceRegistry;
use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\Document\Product as ProductDocument;
use Sylius\Component\Core\Model\Image;
use Sylius\Component\Core\Model\ProductInterface;
use Tests\Setono\SyliusAlgoliaPlugin\Stubs\Entity\Channel;
use Tests\Setono\SyliusAlgoliaPlugin\Stubs\Entity\Product;
use Tests\Setono\SyliusAlgoliaPlugin\Stubs\Entity\Taxon;

/**
 * @covers \Setono\SyliusAlgoliaPlugin\Config\IndexableResourceRegistry
 */
final class IndexableResourceRegistryTest extends TestCase
{
    /**
     * @test
     */
    public function it_adds_on_instantiation(): void
    {
        $indexableResource1 = new IndexableResource('sylius.product', Product::class, ProductDocument::class);
        $indexableResource2 = new IndexableResource('sylius.taxon', Taxon::class, TaxonDocument::class);

        $registry = new IndexableResourceRegistry();
        $registry->add($indexableResource1);
        $registry->add($indexableResource2);

        $resources = iterator_to_array($registry->getIterator());
        self::assertCount(2, $resources);

        self::assertSame($indexableResource1, $resources['sylius.product']);
        self::assertSame($indexableResource2, $resources['sylius.taxon']);
    }

    /**
     * @test
     */
    public function it_returns_true_when_collection_has_with_class(): void
    {
        $registry = $this->getRegistry();
        self::assertTrue($registry->hasWithClass(Product::class));
        self::assertTrue($registry->hasWithClass(new Product()));
    }

    /**
     * @test
     */
    public function it_returns_false_when_collection_does_not_has_with_class(): void
    {
        $registry = $this->getRegistry();
        self::assertFalse($registry->hasWithClass(Image::class));
    }

    /**
     * @test
     */
    public function it_returns_true_when_collection_has_with_name(): void
    {
        $registry = $this->getRegistry();
        self::assertTrue($registry->hasWithName('sylius.product'));
    }

    /**
     * @test
     */
    public function it_returns_false_when_collection_does_not_has_with_name(): void
    {
        $registry = $this->getRegistry();
        self::assertFalse($registry->hasWithName('sylius.image'));
    }

    /**
     * @test
     */
    public function it_gets_by_name(): void
    {
        $registry = $this->getRegistry();
        $indexableResource = $registry->getByName('sylius.product');
        self::assertSame('sylius.product', $indexableResource->name);
    }

    /**
     * @test
     */
    public function it_gets_by_class(): void
    {
        $registry = $this->getRegistry();
        $indexableResource = $registry->getByClass(Product::class);
        self::assertSame('sylius.product', $indexableResource->name);
    }

    /**
     * @test
     */
    public function it_gets_by_class2(): void
    {
        $registry = $this->getRegistry();
        $indexableResource = $registry->getByClass(ChildChannel::class);
        self::assertSame('sylius.channel', $indexableResource->name);
    }

    /**
     * @test
     */
    public function it_gets_by_interface(): void
    {
        $registry = $this->getRegistry();
        $indexableResource = $registry->getByClass(ProductInterface::class);
        self::assertSame('sylius.product', $indexableResource->name);
    }

    private function getRegistry(): IndexableResourceRegistry
    {
        $registry = new IndexableResourceRegistry();
        $registry->add(new IndexableResource('sylius.product', Product::class, ProductDocument::class));
        $registry->add(new IndexableResource('sylius.taxon', Taxon::class, TaxonDocument::class));
        $registry->add(new IndexableResource('sylius.channel', Channel::class, ChannelDocument::class));

        return $registry;
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
