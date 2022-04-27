<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Document;

/**
 * Should not be final, so it's easier for plugin users to extend it and add more properties
 */
class Product extends Document implements UrlAwareInterface, ImageUrlsAwareInterface
{
    public ?string $name = null;

    /**
     * UNIX timestamp for creation date
     */
    public ?int $createdAt = null;

    public ?string $url = null;

    public ?string $primaryImageUrl = null;

    /**
     * All images (excluding the primary)
     *
     * @var list<string>
     */
    public array $imageUrls = [];

    /** @var array<array-key, string> */
    public array $taxonCodes = [];

    /** @var array<string, array<array-key, string>> */
    public array $taxons = [];

    public ?string $currency = null;

    public ?float $price = null;

    public ?float $originalPrice = null;

    /** @var array<string, array<array-key, string>> */
    public array $options = [];

    public function isOnSale(): bool
    {
        return null !== $this->originalPrice && null !== $this->price && $this->price < $this->originalPrice;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getFilterSet(): string
    {
        return 'sylius_shop_product_thumbnail';
    }

    public function setImageUrls(array $imageUrls): void
    {
        $primaryImageUrl = [] === $imageUrls ? null : array_shift($imageUrls);

        $this->imageUrls = $imageUrls;
        $this->primaryImageUrl = $primaryImageUrl;
    }

    public function addImageUrl(string $imageUrl): void
    {
        $this->imageUrls[] = $imageUrl;
        $this->primaryImageUrl = $this->imageUrls[0];
    }
}
