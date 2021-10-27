<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Event;

use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\HttpFoundation\Response;

final class ProductIndexEvent
{
    public Response $response;

    /** @psalm-readonly */
    public TaxonInterface $taxon;

    /** @psalm-readonly */
    public string $slug;

    /** @psalm-readonly */
    public string $locale;

    public function __construct(Response $response, TaxonInterface $taxon, string $slug, string $locale)
    {
        $this->response = $response;
        $this->taxon = $taxon;
        $this->slug = $slug;
        $this->locale = $locale;
    }
}
