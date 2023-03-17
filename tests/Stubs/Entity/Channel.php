<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusAlgoliaPlugin\Stubs\Entity;

use Setono\SyliusAlgoliaPlugin\Model\IndexableAwareTrait;
use Setono\SyliusAlgoliaPlugin\Model\IndexableInterface;
use Sylius\Component\Core\Model\Channel as BaseChannel;

class Channel extends BaseChannel implements IndexableInterface
{
    use IndexableAwareTrait;
}
