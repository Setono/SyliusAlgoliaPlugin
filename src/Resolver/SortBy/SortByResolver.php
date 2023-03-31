<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Resolver\SortBy;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\Resolver\IndexName\IndexNameResolverInterface;
use Setono\SyliusAlgoliaPlugin\Resolver\ReplicaIndexName\ReplicaIndexNameResolverInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SortByResolver implements SortByResolverInterface
{
    private IndexNameResolverInterface $indexNameResolver;

    private ReplicaIndexNameResolverInterface $replicaIndexNameResolver;

    private TranslatorInterface $translator;

    private LocaleContextInterface $localeContext;

    public function __construct(
        IndexNameResolverInterface $indexNameResolver,
        ReplicaIndexNameResolverInterface $replicaIndexNameResolver,
        TranslatorInterface $translator,
        LocaleContextInterface $localeContext
    ) {
        $this->indexNameResolver = $indexNameResolver;
        $this->replicaIndexNameResolver = $replicaIndexNameResolver;
        $this->translator = $translator;
        $this->localeContext = $localeContext;
    }

    public function resolveFromIndexableResource(IndexableResource $indexableResource, string $locale = null): array
    {
        $locale = $locale ?? $this->localeContext->getLocaleCode();

        $indexName = $this->indexNameResolver->resolve($indexableResource);

        $sortBys = [
            new SortBy(
                $this->translator->trans('setono_sylius_algolia.ui.sort_by.relevance', [], null, $locale),
                $indexName
            ),
        ];

        foreach ($indexableResource->documentClass::getSortableAttributes() as $attribute => $order) {
            $sortBys[] = new SortBy(
                $this->translator->trans(sprintf('setono_sylius_algolia.ui.sort_by.%s_%s', $attribute, $order), [], null, $locale),
                $this->replicaIndexNameResolver->resolveFromIndexNameAndSortableAttribute(
                    $indexName,
                    $attribute,
                    $order
                )
            );
        }

        return $sortBys;
    }
}
