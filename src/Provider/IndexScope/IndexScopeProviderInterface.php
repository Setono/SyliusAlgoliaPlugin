<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\IndexScope;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;

/**
 * The responsibility of the index scope provider is to provide all the relevant index scopes for a given resource,
 * i.e. for the sylius.product resource this could mean a scope for each channel, for each locale and even for each currency
 */
interface IndexScopeProviderInterface
{
    /**
     * This method will provide all the relevant index scopes for a given resource,
     * i.e. for the sylius.product resource this could mean a scope for each channel,
     * for each locale and even for each currency
     *
     * @return iterable<IndexScope>
     */
    public function getAll(IndexableResource $indexableResource): iterable;

    /**
     * Returns an index scope from the application context
     */
    public function getFromContext(IndexableResource $indexableResource): IndexScope;

    /**
     * Returns an index scope based on the given arguments
     */
    public function getFromChannelAndLocaleAndCurrency(
        IndexableResource $indexableResource,
        string $channelCode = null,
        string $localeCode = null,
        string $currencyCode = null
    ): IndexScope;

    public function supports(IndexableResource $indexableResource): bool;
}
