<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\IndexScope;

use Setono\SyliusAlgoliaPlugin\Config\Index;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;

/**
 * The responsibility of the index scope provider is to provide all the relevant index scopes for a given index
 */
interface IndexScopeProviderInterface
{
    /**
     * This method will provide all the relevant index scopes for a given index
     *
     * @return iterable<IndexScope>
     */
    public function getAll(Index $index): iterable;

    /**
     * Returns an index scope from the application context (channel, locale, currency)
     */
    public function getFromContext(Index $index): IndexScope;

    /**
     * Returns an index scope based on the given arguments
     */
    public function getFromChannelAndLocaleAndCurrency(
        Index $index,
        string $channelCode = null,
        string $localeCode = null,
        string $currencyCode = null
    ): IndexScope;

    public function supports(Index $index): bool;
}
