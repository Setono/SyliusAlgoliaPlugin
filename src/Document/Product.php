<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Document;

use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Settings\IndexSettings;

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

    public function onSale(): bool
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

    public static function getDefaultSettings(IndexScope $indexScope): IndexSettings
    {
        $settings = parent::getDefaultSettings($indexScope);

        $settings->searchableAttributes = [
            'code', // usually the code is the SKU. This gives users the opportunity to search directly for a SKU if they know it
            'name',
        ];

        $settings->attributesForFaceting = [
            'filterOnly(taxonCodes)', // this allows us to show products in a given taxon. This is used in product lists
            'onSale', // this will allow users to filter for products that are on sale
            'price', // this will allow you to create a price slider
        ];

        $settings->customRanking = ['desc(createdAt)'];
        $settings->disablePrefixOnAttributes = ['code'];
        $settings->ignorePlurals = true;
        $settings->allowTyposOnNumericTokens = false;

        return $settings;
    }
}
