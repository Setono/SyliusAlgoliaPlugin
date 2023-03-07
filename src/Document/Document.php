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

    /**
     * This is the code in the database.
     * Used together with the $resourceName, you can identify a given entity in your own database
     */
    public ?string $code = null;

    /**
     * This is the name of the resource, for the product entity in Sylius, this would be 'sylius.product' for example
     */
    public ?string $resourceName = null;

    /**
     * This allows us to always be able to instantiate an extending class without worrying about constructor arguments
     */
    final public function __construct()
    {
    }
}
