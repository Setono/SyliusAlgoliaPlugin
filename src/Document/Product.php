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
     * All images (including the primary image url)
     *
     * @var list<string>
     */
    public array $imageUrls = [];

    /**
     * Holds a list of taxon codes. This makes it easy to filter by a taxon.
     *
     * @var list<string>
     */
    public array $taxonCodes = [];

    public ?string $currency = null;

    public ?float $price = null;

    public ?float $originalPrice = null;

    public function isOnSale(): bool
    {
        return null !== $this->originalPrice && null !== $this->price && $this->price < $this->originalPrice;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function setImageUrls(array $imageUrls): void
    {
        $this->imageUrls = $imageUrls;
        $this->primaryImageUrl = null;

        if (count($imageUrls) > 0) {
            $this->primaryImageUrl = $imageUrls[0];
        }
    }

    public function addImageUrl(string $imageUrl): void
    {
        $this->imageUrls[] = $imageUrl;
        $this->primaryImageUrl = $this->imageUrls[0];
    }
}
