<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusAlgoliaPlugin\Config;

use PHPUnit\Framework\TestCase;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\Document\Product as ProductDocument;
use Tests\Setono\SyliusAlgoliaPlugin\Stubs\Entity\Product;

/**
 * @covers \Setono\SyliusAlgoliaPlugin\Config\IndexableResource
 */
final class IndexableResourceTest extends TestCase
{
    /**
     * @test
     */
    public function it_instantiates(): void
    {
        $indexableResource = new IndexableResource('sylius.product', Product::class, ProductDocument::class);

        self::assertSame('sylius.product', $indexableResource->name);
        self::assertSame(Product::class, $indexableResource->resourceClass);
        self::assertSame('product', $indexableResource->shortName);
    }

    /**
     * @test
     */
    public function it_throws_if_class_is_not_a_resource(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        /** @psalm-suppress InvalidArgument */
        new IndexableResource('test', NotImplementingResource::class, ProductDocument::class);
    }
}

class NotImplementingResource
{
}
