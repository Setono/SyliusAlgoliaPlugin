<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper;

use Setono\SyliusAlgoliaPlugin\Document\DocumentInterface;
use Setono\SyliusAlgoliaPlugin\Document\PopulateUrlInterface;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

final class UrlDataMapper implements DataMapperInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function map(ResourceInterface $source, DocumentInterface $target, IndexScope $indexScope, array $context = []): void
    {
        Assert::true($this->supports($source, $target, $indexScope, $context), 'The given $source and $target is not supported');

        $target->populateUrl($this->urlGenerator, $source, $indexScope->localeCode);
    }

    /**
     * @psalm-assert-if-true PopulateUrlInterface $target
     * @psalm-assert-if-true !null $indexScope->localeCode
     */
    public function supports(ResourceInterface $source, DocumentInterface $target, IndexScope $indexScope, array $context = []): bool
    {
        return $target instanceof PopulateUrlInterface && null !== $indexScope->localeCode;
    }
}
