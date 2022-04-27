<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper;

use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

final class CodeDataMapper implements DataMapperInterface
{
    public function map(ResourceInterface $source, Document $target, IndexScope $indexScope, array $context = []): void
    {
        Assert::true(
            $this->supports($source, $target, $indexScope, $context),
            'The given $source and $target is not supported'
        );

        $target->code = $source->getCode();
    }

    /**
     * @psalm-assert-if-true CodeAwareInterface $source
     */
    public function supports(
        ResourceInterface $source,
        Document $target,
        IndexScope $indexScope,
        array $context = []
    ): bool {
        return $source instanceof CodeAwareInterface;
    }
}
