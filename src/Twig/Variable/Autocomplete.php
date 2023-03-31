<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Twig\Variable;

use Setono\SyliusAlgoliaPlugin\Javascript\Autocomplete\Source;
use Setono\SyliusAlgoliaPlugin\Javascript\Autocomplete\SourcesResolverInterface;

final class Autocomplete
{
    private SourcesResolverInterface $sourcesResolver;

    public function __construct(SourcesResolverInterface $sourcesResolver)
    {
        $this->sourcesResolver = $sourcesResolver;
    }

    /**
     * @return list<Source>
     */
    public function getSources(): array
    {
        return $this->sourcesResolver->getSources();
    }
}
