<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper;

use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Model\IndexableInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

final class IndexableDataMapper implements DataMapperInterface
{
    /**
     * @param ResourceInterface|IndexableInterface $source
     * @param array<string, mixed> $context
     */
    public function map(
        ResourceInterface $source,
        Document $target,
        IndexScope $indexScope,
        array $context = []
    ): void {
        Assert::true(
            $this->supports($source, $target, $indexScope, $context),
            'The given $source and $target is not supported'
        );

        $target->objectId = $source->getObjectId();
        $target->code = $source->getCode();
    }

    /**
     * @psalm-assert-if-true IndexableInterface $source
     */
    public function supports(
        ResourceInterface $source,
        Document $target,
        IndexScope $indexScope,
        array $context = []
    ): bool {
        return $source instanceof IndexableInterface;
    }
}
