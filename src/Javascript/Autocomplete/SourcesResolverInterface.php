<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Javascript\Autocomplete;

interface SourcesResolverInterface
{
    /**
     * Will return a list of sources based on the current application context
     *
     * @return list<Source>
     */
    public function getSources(): array;
}
