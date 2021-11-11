<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Repository;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Setono\SyliusAlgoliaPlugin\DTO\ProductIndexScope;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @mixin EntityRepository
 */
trait ProductRepositoryTrait
{
    public function createQueryBuilderFromProductIndexScope(ProductIndexScope $productIndexScope): QueryBuilder
    {
        // todo recheck this query
        $qb = $this
            ->createQueryBuilder('product')
            ->innerJoin('product.channels', 'channels', Join::WITH, 'channels.code = :channelCode')
            ->innerJoin('product.translations', 'translations', Join::WITH, 'translations.locale = :localeCode')
            ->setParameter('channelCode', $productIndexScope->channel)
            ->setParameter('localeCode', $productIndexScope->locale)
        ;

        return $qb;
    }
}
