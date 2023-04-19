<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusAlgoliaPlugin\Resolver\IndexName;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusAlgoliaPlugin\Config\Index;
use Setono\SyliusAlgoliaPlugin\Config\IndexRegistry;
use Setono\SyliusAlgoliaPlugin\Document\Product;
use Setono\SyliusAlgoliaPlugin\Indexer\IndexerInterface;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Provider\IndexScope\IndexScopeProviderInterface;
use Setono\SyliusAlgoliaPlugin\Resolver\IndexName\IndexNameResolver;

/**
 * @covers \Setono\SyliusAlgoliaPlugin\Resolver\IndexName\IndexNameResolver
 */
final class IndexNameResolverTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_resolves_from_index_scope(): void
    {
        $index = new Index(
            'products',
            Product::class,
            $this->prophesize(IndexerInterface::class)->reveal(),
            [],
            'prefix'
        );

        $resolver = new IndexNameResolver(
            new IndexRegistry(),
            $this->prophesize(IndexScopeProviderInterface::class)->reveal(),
            'prod'
        );

        $indexScope = (new IndexScope($index))
            ->withChannelCode('FASHION_WEB')
            ->withLocaleCode('en_US')
            ->withCurrencyCode('USD')
        ;

        self::assertSame('prefix__prod__products__fashion_web__en_us__usd', $resolver->resolveFromIndexScope($indexScope));
    }
}
