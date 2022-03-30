<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\UrlGenerator;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class AbstractResourceUrlGenerator implements ResourceUrlGeneratorInterface
{
    protected UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }
}
