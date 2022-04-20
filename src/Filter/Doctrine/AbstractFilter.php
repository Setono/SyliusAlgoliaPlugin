<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Filter\Doctrine;

use Doctrine\ORM\QueryBuilder;
use Webmozart\Assert\Assert;

abstract class AbstractFilter implements FilterInterface
{
    protected function getRootAlias(QueryBuilder $qb): string
    {
        $aliases = $qb->getRootAliases();
        Assert::notEmpty($aliases);

        return $aliases[0];
    }
}
