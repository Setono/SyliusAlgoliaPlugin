<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Document;

use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Implement this interface in your document to easily populate a corresponding URL for your document
 *
 * todo I don't like the name of this interface and the PopulateImageUrlInterface
 */
interface PopulateUrlInterface
{
    /**
     * Should populate the url property of your document
     */
    public function populateUrl(UrlGeneratorInterface $urlGenerator, ResourceInterface $source, string $locale): void;
}
