<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusAlgoliaPlugin\Stubs\Entity;

use Setono\SyliusAlgoliaPlugin\Model\IndexableAwareTrait;
use Setono\SyliusAlgoliaPlugin\Model\IndexableInterface;
use Sylius\Component\Core\Model\Taxon as BaseTaxon;

class Taxon extends BaseTaxon implements IndexableInterface
{
    use IndexableAwareTrait;
}
