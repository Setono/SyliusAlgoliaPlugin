<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Repository;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Model\IndexableInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @mixin EntityRepository
 */
trait ProductRepositoryTrait
{
    public function createIndexableCollectionQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o');
    }

    /**
     * @param list<mixed> $ids
     *
     * @return list<IndexableInterface>
     */
    public function findFromIndexScopeAndIds(IndexScope $indexScope, array $ids): array
    {
        // todo check this query
        $qb = $this->createQueryBuilder('product');

        if (null !== $indexScope->channelCode) {
            $qb->innerJoin('product.channels', 'channels', Join::WITH, 'channels.code = :channelCode')
                ->setParameter('channelCode', $indexScope->channelCode)
            ;
        }

        if (null !== $indexScope->localeCode) {
            $qb->innerJoin('product.translations', 'translations', Join::WITH, 'translations.locale = :localeCode')
                ->setParameter('localeCode', $indexScope->localeCode)
            ;
        }

        $qb->andWhere('product.id IN (:ids)')
            ->setParameter('ids', $ids)
        ;

        return $qb->getQuery()->getResult();
    }
}
