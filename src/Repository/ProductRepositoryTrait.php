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
        $qb = $this->createQueryBuilder('product');
        $qb->innerJoin('product.channels', 'channels', Join::WITH, 'channels.code = :channelCode');
        $qb->innerJoin('product.translations', 'translations', Join::WITH, 'translations.locale = :localeCode');
        $qb->setParameter('channelCode', $productIndexScope->channelCode);
        $qb->setParameter('localeCode', $productIndexScope->localeCode);

        return $qb;
    }
}
