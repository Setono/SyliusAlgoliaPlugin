<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusAlgoliaPlugin\Application\Repository;

use Setono\SyliusAlgoliaPlugin\Repository\IndexableResourceRepositoryInterface;
use Setono\SyliusAlgoliaPlugin\Repository\TaxonRepositoryTrait;
use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonRepository as BaseTaxonRepository;

class TaxonRepository extends BaseTaxonRepository implements IndexableResourceRepositoryInterface
{
    use TaxonRepositoryTrait;
}
