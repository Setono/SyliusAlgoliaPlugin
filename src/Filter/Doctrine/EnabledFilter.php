<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Filter\Doctrine;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Sylius\Component\Resource\Model\ToggleableInterface;

final class EnabledFilter extends AbstractFilter
{
    public function apply(QueryBuilder $qb, IndexableResource $indexableResource): void
    {
        if (!is_a($indexableResource->resourceClass, ToggleableInterface::class)) {
            return;
        }

        $rootAlias = $this->getRootAlias($qb);

        $qb->andWhere(sprintf('%s.enabled = true', $rootAlias));
    }
}
