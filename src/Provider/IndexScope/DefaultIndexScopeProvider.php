<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\IndexScope;

use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;

final class DefaultIndexScopeProvider implements IndexScopeProviderInterface
{
    public function getIndexScopes(): iterable
    {
        yield new IndexScope();
    }

    public function supports($resource): bool
    {
        return true;
    }
}
