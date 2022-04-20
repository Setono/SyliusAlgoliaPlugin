<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\UrlGenerator;

use Sylius\Component\Resource\Model\ResourceInterface;

final class CompositeResourceUrlGenerator implements ResourceUrlGeneratorInterface
{
    /** @var list<ResourceUrlGeneratorInterface> */
    private array $generators = [];

    public function add(ResourceUrlGeneratorInterface $resourceUrlGenerator): void
    {
        $this->generators[] = $resourceUrlGenerator;
    }

    public function generate(ResourceInterface $resource, array $context = []): string
    {
        foreach ($this->generators as $generator) {
            if ($generator->supports($resource, $context)) {
                return $generator->generate($resource, $context);
            }
        }

        throw new \RuntimeException(sprintf('No url generators supports the given resource %s', get_class($resource)));
    }

    public function supports(ResourceInterface $resource, array $context = []): bool
    {
        foreach ($this->generators as $generator) {
            if ($generator->supports($resource, $context)) {
                return true;
            }
        }

        return false;
    }
}
