<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Document;

interface DocumentInterface
{
    /**
     * This will be the object id in Algolia. This MUST be unique across the index therefore if you mix
     * products and taxons for example, use a prefix on the object id
     *
     * @return mixed
     */
    public function getId();
}
