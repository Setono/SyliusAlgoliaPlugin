<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Document;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

/**
 * Should not be final, so it's easier for plugin users to extend it and add more properties
 */
class Product implements DocumentInterface, PopulateUrlInterface
{
    public ?int $id = null;

    public ?string $name = null;

    public ?string $url = null;

    public ?string $imageUrl = null;

    /** @var array<array-key, string> */
    public array $taxonCodes = [];

    /**
     * todo should be built into: https://www.algolia.com/doc/guides/managing-results/refine-results/faceting/#hierarchical-facets
     *
     * @var array<array-key, string>
     */
    public array $taxons = [];

    public ?string $baseCurrency = null;

    public ?float $basePrice = null;

    /**
     * Example:
     *
     * [
     *     'EUR' => 98.32,
     *     'USD' => 103.92
     * ]
     *
     * @var array<string, float>
     */
    public array $prices = [
        'EUR' => 98.32,
        'USD' => 103.92,
    ];

    public function getId(): int
    {
        return (int) $this->id;
    }

    /**
     * @param ProductInterface|ResourceInterface $source
     */
    public function populateUrl(UrlGeneratorInterface $urlGenerator, ResourceInterface $source, string $locale): void
    {
        Assert::isInstanceOf($source, ProductInterface::class);

        $this->url = $urlGenerator->generate('sylius_shop_product_show', [
            'slug' => $source->getTranslation($locale)->getSlug(),
            '_locale' => $locale,
        ]);
    }
}
