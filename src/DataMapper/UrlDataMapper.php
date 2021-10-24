<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper;

use Psl;
use Setono\SyliusAlgoliaPlugin\Document\DocumentInterface;
use Setono\SyliusAlgoliaPlugin\Document\PopulateUrlInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class UrlDataMapper implements DataMapperInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function map(ResourceInterface $source, DocumentInterface $target, array $context = []): void
    {
        Psl\invariant($this->supports($source, $target, $context), 'The given $source and $target is not supported');

        /** @var LocaleInterface $locale */
        $locale = $context['locale'];
        $localeCode = (string) $locale->getCode();

        $target->populateUrl($this->urlGenerator, $source, $localeCode);
    }

    /**
     * @psalm-assert-if-true PopulateUrlInterface $target
     * @psalm-assert-if-true LocaleInterface $context['locale']
     */
    public function supports(ResourceInterface $source, DocumentInterface $target, array $context = []): bool
    {
        return $target instanceof PopulateUrlInterface
            && isset($context['locale'])
            && $context['locale'] instanceof LocaleInterface
        ;
    }
}