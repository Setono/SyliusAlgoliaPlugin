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
     * This is the FQCN for the document being sent to Algolia. This makes it a lot easier to deserialize the JSON
     * when it comes back from Algolia since we know which class to deserialize to
     *
     * @var class-string<Document>
     */
    public string $documentClass;

    /**
     * This allows us to always be able to instantiate an extending class without worrying about constructor arguments
     */
    final public function __construct()
    {
        $this->documentClass = static::class;
    }

    /**
     * MUST return an array indexed by the attribute name and the sort order as the value, e.g.
     *
     * [
     *   'price' => 'asc',
     *   'createdAt => 'desc'
     * ]
     *
     * NOTE that this is not applied to the customRanking setting, but is used to create replica indexes where the
     * ranking setting will match your sorting. The above example would result in two replica indexes with
     * ranking as asc(price) and desc(createdAt) respectively
     *
     * @return array<string, string>
     */
    public static function getSortableAttributes(): array
    {
        return [];
    }
}
