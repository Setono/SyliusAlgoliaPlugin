<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\UrlGenerator;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

final class ProductUrlGenerator extends AbstractResourceUrlGenerator
{
    /**
     * @param ProductInterface|ResourceInterface $resource
     */
    public function generate(ResourceInterface $resource, array $context = []): string
    {
        Assert::true($this->supports($resource, $context));

        return $this->urlGenerator->generate('sylius_shop_product_show', [
            'slug' => $resource->getTranslation($context['localeCode'])->getSlug(),
            '_locale' => $context['localeCode'],
        ]);
    }

    /**
     * @psalm-assert-if-true ProductInterface $resource
     * @psalm-assert-if-true string $context['localeCode']
     */
    public function supports(ResourceInterface $resource, array $context = []): bool
    {
        return $resource instanceof ProductInterface && isset($context['localeCode']) && is_string($context['localeCode']);
    }
}
