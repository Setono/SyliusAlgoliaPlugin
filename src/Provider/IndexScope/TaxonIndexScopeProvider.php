<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\IndexScope;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class TaxonIndexScopeProvider implements IndexScopeProviderInterface
{
    private RepositoryInterface $localeRepository;

    private LocaleContextInterface $localeContext;

    public function __construct(
        RepositoryInterface $localeRepository,
        LocaleContextInterface $localeContext
    ) {
        $this->localeRepository = $localeRepository;
        $this->localeContext = $localeContext;
    }

    public function getAll(IndexableResource $indexableResource): iterable
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();

        foreach ($locales as $locale) {
            yield (new IndexScope($indexableResource))->withLocaleCode($locale->getCode());
        }
    }

    public function getFromContext(IndexableResource $indexableResource): IndexScope
    {
        return $this->getFromChannelAndLocaleAndCurrency(
            $indexableResource,
            null,
            $this->localeContext->getLocaleCode(),
        );
    }

    public function getFromChannelAndLocaleAndCurrency(
        IndexableResource $indexableResource,
        string $channelCode = null,
        string $localeCode = null,
        string $currencyCode = null
    ): IndexScope {
        return (new IndexScope($indexableResource))
            ->withLocaleCode($localeCode)
        ;
    }

    public function supports(IndexableResource $indexableResource): bool
    {
        return is_a($indexableResource->resourceClass, TaxonInterface::class, true);
    }
}
