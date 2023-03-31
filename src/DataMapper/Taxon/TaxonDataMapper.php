<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper\Taxon;

use Setono\SyliusAlgoliaPlugin\DataMapper\DataMapperInterface;
use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\Document\Taxon;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

final class TaxonDataMapper implements DataMapperInterface
{
    /**
     * @param TaxonInterface|ResourceInterface $source
     * @param Taxon|Document $target
     * @param array<string, mixed> $context
     */
    public function map(ResourceInterface $source, Document $target, IndexScope $indexScope, array $context = []): void
    {
        Assert::true(
            $this->supports($source, $target, $indexScope, $context),
            'The given $source and $target is not supported'
        );

        $target->name = $source->getTranslation($indexScope->localeCode)->getName();
    }

    /**
     * @psalm-assert-if-true TaxonInterface $source
     * @psalm-assert-if-true Taxon $target
     * @psalm-assert-if-true !null $indexScope->localeCode
     */
    public function supports(
        ResourceInterface $source,
        Document $target,
        IndexScope $indexScope,
        array $context = []
    ): bool {
        return $source instanceof TaxonInterface
            && $target instanceof Taxon
            && null !== $indexScope->localeCode;
    }
}
