<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Document;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Implement this interface in your document to easily populate a corresponding image URL for your document
 */
interface PopulateImageUrlInterface
{
    /**
     * Should populate the image url property of your document
     */
    public function populateImageUrl(CacheManager $cacheManager, ResourceInterface $source): void;
}
