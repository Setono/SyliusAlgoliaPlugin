<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Document;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

/**
 * Should not be final, so it's easier for plugin users to extend it and add more properties
 */
class Product implements DocumentInterface, PopulateUrlInterface, PopulateImageUrlInterface
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

    public ?float $originalBasePrice = null;

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
    public array $prices = [];

    /**
     * Example:
     *
     * [
     *     'EUR' => 109.95,
     *     'USD' => 114.95
     * ]
     *
     * @var array<string, float>
     */
    public array $originalPrices = [];

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

    /**
     * @param ProductInterface|ResourceInterface $source
     */
    public function populateImageUrl(CacheManager $cacheManager, ResourceInterface $source): void
    {
        Assert::isInstanceOf($source, ProductInterface::class);

        foreach ($source->getImages() as $image) {
            $this->imageUrl = $cacheManager->getBrowserPath(
                (string) $image->getPath(),
                'sylius_shop_product_large_thumbnail',
                [],
                null,
                UrlGeneratorInterface::ABSOLUTE_PATH
            );

            break;
        }
    }
}
