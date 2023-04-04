<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\IndexScope;

use Setono\SyliusAlgoliaPlugin\Config\Index;
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

    public function getAll(Index $index): iterable
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();

        foreach ($locales as $locale) {
            yield (new IndexScope($index))->withLocaleCode($locale->getCode());
        }
    }

    public function getFromContext(Index $index): IndexScope
    {
        return $this->getFromChannelAndLocaleAndCurrency(
            $index,
            null,
            $this->localeContext->getLocaleCode(),
        );
    }

    public function getFromChannelAndLocaleAndCurrency(
        Index $index,
        string $channelCode = null,
        string $localeCode = null,
        string $currencyCode = null
    ): IndexScope {
        return (new IndexScope($index))
            ->withLocaleCode($localeCode)
        ;
    }

    public function supports(Index $index): bool
    {
        return $index->hasResourceWithClass(TaxonInterface::class);
    }
}
