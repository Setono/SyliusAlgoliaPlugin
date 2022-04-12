<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\Document\ImageUrlsAwareInterface;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Sylius\Component\Core\Model\ImagesAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

final class ImageUrlsDataMapper implements DataMapperInterface
{
    private CacheManager $cacheManager;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function map(
        ResourceInterface $source,
        Document $target,
        IndexScope $indexScope,
        array $context = []
    ): void {
        Assert::true(
            $this->supports($source, $target, $indexScope, $context),
            'The given $source and $target is not supported'
        );

        $imageUrls = [];

        foreach ($source->getImages() as $image) {
            $imageUrls[] = $this->cacheManager->getBrowserPath(
                (string) $image->getPath(),
                $target->getFilterSet(),
                [],
                null,
                UrlGeneratorInterface::ABSOLUTE_PATH
            );
        }

        $target->setImageUrls($imageUrls);
    }

    /**
     * @psalm-assert-if-true ImagesAwareInterface $source
     * @psalm-assert-if-true ImageUrlsAwareInterface $target
     */
    public function supports(
        ResourceInterface $source,
        Document $target,
        IndexScope $indexScope,
        array $context = []
    ): bool {
        return $source instanceof ImagesAwareInterface && $target instanceof ImageUrlsAwareInterface;
    }
}
