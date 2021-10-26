<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Psl;
use Setono\SyliusAlgoliaPlugin\Document\DocumentInterface;
use Setono\SyliusAlgoliaPlugin\Document\PopulateImageUrlInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

final class ImageUrlDataMapper implements DataMapperInterface
{
    private CacheManager $cacheManager;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function map(ResourceInterface $source, DocumentInterface $target, array $context = []): void
    {
        Psl\invariant($this->supports($source, $target, $context), 'The given $source and $target is not supported');

        $target->populateImageUrl($this->cacheManager, $source);
    }

    /**
     * @psalm-assert-if-true PopulateImageUrlInterface $target
     */
    public function supports(ResourceInterface $source, DocumentInterface $target, array $context = []): bool
    {
        return $target instanceof PopulateImageUrlInterface;
    }
}
