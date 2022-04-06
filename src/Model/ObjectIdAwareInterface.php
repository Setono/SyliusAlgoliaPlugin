<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Model;

interface ObjectIdAwareInterface
{
    /**
     * This will be the object id in Algolia. This MUST be unique across the index therefore if you mix
     * products and taxons in an index for example, use a prefix on the object id
     */
    public function getObjectId(): string;
}
