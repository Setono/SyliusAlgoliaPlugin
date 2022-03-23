<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\IndexScope;

use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Registry\SupportsResourceAwareInterface;

interface IndexScopeProviderInterface extends SupportsResourceAwareInterface
{
    /**
     * @return iterable<IndexScope>
     */
    public function getIndexScopes(): iterable;
}
