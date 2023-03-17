<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Model;

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface IndexableInterface extends ResourceInterface, CodeAwareInterface
{
    /**
     * This will be the object id in Algolia. This MUST be unique across the index therefore if you mix
     * products and taxons in an index for example, use a prefix on the object id
     */
    public function getObjectId(): string;
}
