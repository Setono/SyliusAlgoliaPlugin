<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\UrlGenerator;

use Sylius\Component\Resource\Model\ResourceInterface;

interface ResourceUrlGeneratorInterface
{
    /**
     * @param array<string, mixed> $context
     */
    public function generate(ResourceInterface $resource, array $context = []): string;

    /**
     * @param array<string, mixed> $context
     */
    public function supports(ResourceInterface $resource, array $context = []): bool;
}
