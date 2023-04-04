<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\IndexScope;

use Setono\SyliusAlgoliaPlugin\Config\Index;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;

final class CompositeIndexScopeProvider implements IndexScopeProviderInterface
{
    /** @var list<IndexScopeProviderInterface> */
    private array $providers = [];

    public function add(IndexScopeProviderInterface $indexScopeProvider): void
    {
        $this->providers[] = $indexScopeProvider;
    }

    public function getAll(Index $index): iterable
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($index)) {
                yield from $provider->getAll($index);

                return;
            }
        }

        throw new \RuntimeException('Unsupported resource'); // todo better exception
    }

    public function getFromContext(Index $index): IndexScope
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($index)) {
                return $provider->getFromContext($index);
            }
        }

        throw new \RuntimeException('Unsupported resource'); // todo better exception
    }

    public function getFromChannelAndLocaleAndCurrency(
        Index $index,
        string $channelCode = null,
        string $localeCode = null,
        string $currencyCode = null
    ): IndexScope {
        foreach ($this->providers as $provider) {
            if ($provider->supports($index)) {
                return $provider->getFromChannelAndLocaleAndCurrency($index, $channelCode, $localeCode, $currencyCode);
            }
        }

        throw new \RuntimeException('Unsupported index'); // todo better exception
    }

    public function supports(Index $index): bool
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($index)) {
                return true;
            }
        }

        return false;
    }
}
