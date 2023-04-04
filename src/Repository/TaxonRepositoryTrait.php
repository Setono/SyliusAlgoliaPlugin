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
trait TaxonRepositoryTrait
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
        $qb = $this->createQueryBuilder('taxon');

        if (null !== $indexScope->localeCode) {
            $qb->innerJoin('taxon.translations', 'translations', Join::WITH, 'translations.locale = :localeCode')
                ->setParameter('localeCode', $indexScope->localeCode)
            ;
        }

        $qb->andWhere('taxon.id IN (:ids)')
            ->setParameter('ids', $ids)
        ;

        return $qb->getQuery()->getResult();
    }
}
