<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\IndexScope;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;

final class CompositeIndexScopeProvider implements IndexScopeProviderInterface
{
    /** @var list<IndexScopeProviderInterface> */
    private array $providers = [];

    public function add(IndexScopeProviderInterface $indexScopeProvider): void
    {
        $this->providers[] = $indexScopeProvider;
    }

    public function getAll(IndexableResource $indexableResource): iterable
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($indexableResource)) {
                yield from $provider->getAll($indexableResource);

                return;
            }
        }

        throw new \RuntimeException('Unsupported resource'); // todo better exception
    }

    public function getFromContext(IndexableResource $indexableResource): IndexScope
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($indexableResource)) {
                return $provider->getFromContext($indexableResource);
            }
        }

        throw new \RuntimeException('Unsupported resource'); // todo better exception
    }

    public function getFromChannelAndLocaleAndCurrency(
        IndexableResource $indexableResource,
        string $channelCode = null,
        string $localeCode = null,
        string $currencyCode = null
    ): IndexScope {
        foreach ($this->providers as $provider) {
            if ($provider->supports($indexableResource)) {
                return $provider->getFromChannelAndLocaleAndCurrency($indexableResource, $channelCode, $localeCode, $currencyCode);
            }
        }

        throw new \RuntimeException('Unsupported resource'); // todo better exception
    }

    public function supports(IndexableResource $indexableResource): bool
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($indexableResource)) {
                return true;
            }
        }

        return false;
    }
}
