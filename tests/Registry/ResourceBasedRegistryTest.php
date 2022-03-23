<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusAlgoliaPlugin\Registry;

use PHPUnit\Framework\TestCase;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\Registry\ResourceBasedRegistry;
use Setono\SyliusAlgoliaPlugin\Registry\SupportsResourceAwareInterface;
use Sylius\Component\Core\Model\Product;

/**
 * @covers \Setono\SyliusAlgoliaPlugin\Registry\ResourceBasedRegistry
 */
final class ResourceBasedRegistryTest extends TestCase
{
    /**
     * @test
     */
    public function it_prioritizes(): void
    {
        $registry = new ResourceBasedRegistry(SupportsResourceAwareInterface::class);

        // both services support any resource
        $service1 = new Service(true);
        $service2 = new Service(true);

        $registry->register($service1, 0);
        $registry->register($service2, 1); // this service has higher priority

        $service = $registry->get(new IndexableResource('sylius.product', Product::class));

        self::assertSame($service2, $service);
    }
}

class Service implements SupportsResourceAwareInterface
{
    private bool $supports;

    public function __construct(bool $supports)
    {
        $this->supports = $supports;
    }

    public function supports($resource): bool
    {
        return $this->supports;
    }
}
