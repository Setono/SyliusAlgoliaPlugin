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

    public ?string $code = null;

    public ?string $name = null;

    /**
     * UNIX timestamp for creation date
     */
    public ?int $createdAt = null;

    public ?string $url = null;

    public ?string $imageUrl = null;

    /** @var array<array-key, string> */
    public array $taxonCodes = [];

    /** @var array<string, array<array-key, string>> */
    public array $taxons = [];

    public ?string $currency = null;

    public ?float $price = null;

    public ?float $originalPrice = null;

    /** @var array<string, array<array-key, string>> */
    public array $options = [];

    public function getId(): int
    {
        return (int) $this->id;
    }

    public function isOnSale(): bool
    {
        return null !== $this->originalPrice && null !== $this->price && $this->price < $this->originalPrice;
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
