<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\UrlGenerator;

use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

final class TaxonUrlGenerator extends AbstractResourceUrlGenerator
{
    /**
     * @param TaxonInterface|ResourceInterface $resource
     * @param array<string, mixed> $context
     */
    public function generate(ResourceInterface $resource, array $context = []): string
    {
        Assert::true($this->supports($resource, $context));

        return $this->urlGenerator->generate('sylius_shop_product_index', [
            'slug' => $resource->getTranslation($context['localeCode'])->getSlug(),
            '_locale' => $context['localeCode'],
        ]);
    }

    /**
     * @psalm-assert-if-true TaxonInterface $resource
     * @psalm-assert-if-true string $context['localeCode']
     */
    public function supports(ResourceInterface $resource, array $context = []): bool
    {
        return $resource instanceof TaxonInterface && isset($context['localeCode']) && is_string($context['localeCode']);
    }
}
