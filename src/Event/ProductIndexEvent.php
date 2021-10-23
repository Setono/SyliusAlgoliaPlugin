<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Event;

use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\HttpFoundation\Response;

final class ProductIndexEvent
{
    public ?Response $response = null;

    /** @psalm-readonly */
    public TaxonInterface $taxon;

    /** @psalm-readonly */
    public string $slug;

    /** @psalm-readonly */
    public string $locale;

    public function __construct(TaxonInterface $taxon, string $slug, string $locale)
    {
        $this->taxon = $taxon;
        $this->slug = $slug;
        $this->locale = $locale;
    }
}
