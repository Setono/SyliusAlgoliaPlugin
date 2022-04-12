<?php
declare(strict_types=1);

namespace Tests\Setono\SyliusAlgoliaPlugin\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setono\SyliusAlgoliaPlugin\Model\ObjectIdAwareInterface;
use Setono\SyliusAlgoliaPlugin\Model\ObjectIdAwareTrait;
use Sylius\Component\Core\Model\Product as BaseProduct;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_product")
 */
class Product extends BaseProduct implements ObjectIdAwareInterface
{
    use ObjectIdAwareTrait;
}
