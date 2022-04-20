<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DataMapper;

use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Sylius\Component\Resource\Model\ResourceInterface;

final class CompositeDataMapper implements DataMapperInterface
{
    /** @var list<DataMapperInterface> */
    private array $dataMappers = [];

    public function add(DataMapperInterface $dataMapper): void
    {
        $this->dataMappers[] = $dataMapper;
    }

    public function map(ResourceInterface $source, Document $target, IndexScope $indexScope, array $context = []): void
    {
        foreach ($this->dataMappers as $dataMapper) {
            if ($dataMapper->supports($source, $target, $indexScope, $context)) {
                $dataMapper->map($source, $target, $indexScope, $context);
            }
        }
    }

    public function supports(ResourceInterface $source, Document $target, IndexScope $indexScope, array $context = []): bool
    {
        foreach ($this->dataMappers as $dataMapper) {
            if ($dataMapper->supports($source, $target, $indexScope, $context)) {
                return true;
            }
        }

        return false;
    }
}
