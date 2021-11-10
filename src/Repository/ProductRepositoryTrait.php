<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Repository;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Setono\SyliusAlgoliaPlugin\Model\ResolvedProductIndexInterface;

trait ProductRepositoryTrait
{
    public function createQueryBuilderForResolvedIndex(ResolvedProductIndexInterface $resolvedProductIndex): QueryBuilder
    {
        $qb = $this->createQueryBuilder('product');

        if (null !== $resolvedProductIndex->getChannel()) {
            $qb->andWhere(':channel MEMBER OF product.channels');
            $qb->setParameter('channel', $resolvedProductIndex->getChannel());
        }

        // I'm not sure what to do with currency, as channel pricing is defined in baseChannelCurrency
        // All products should have the price existing in that currency anyway

        if (null !== $resolvedProductIndex->getLocale()) {
            $qb->innerJoin('product.translations', 'translations', Join::WITH, 'translations.locale = :localeCode');
            $qb->setParameter('localeCode', $resolvedProductIndex->getLocale()->getCode());
        }

        return $qb;
    }
}
