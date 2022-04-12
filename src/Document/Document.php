<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Document;

/**
 * All documents should extend this class
 */
abstract class Document
{
    /**
     * This will be the object id in Algolia. This MUST be unique across the index therefore if you mix
     * products and taxons for example, use a prefix on the object id
     */
    public ?string $objectId = null;

    final public function __construct()
    {
    }
}
