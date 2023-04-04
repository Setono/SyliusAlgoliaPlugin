<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Config;

use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\Exception\NonExistingResourceException;
use Setono\SyliusAlgoliaPlugin\Indexer\IndexerInterface;
use Webmozart\Assert\Assert;

final class Index
{
    /**
     * This is the name you gave the index in the configuration.
     * This name is also used when resolving the final index name in Algolia, so do not change this unless you know what you're doing
     */
    public string $name;

    /**
     * This is the FQCN for the document that is mapped to an index in Algolia.
     * If you are indexing products this could be Setono\SyliusAlgoliaPlugin\Document\Product
     *
     * @var class-string<Document>
     */
    public string $document;

    public IndexerInterface $indexer;

    /**
     * An array of resources, indexed by the resource name
     *
     * @var array<string, IndexableResource>
     */
    public array $resources;

    /**
     * @param class-string<Document> $document
     * @param array<string, IndexableResource> $resources
     */
    public function __construct(string $name, string $document, IndexerInterface $indexer, array $resources)
    {
        Assert::stringNotEmpty($name);

        if (!is_a($document, Document::class, true)) {
            throw new \InvalidArgumentException(sprintf(
                'The document class %s MUST be an instance of %s',
                $document,
                Document::class
            ));
        }

        $this->name = $name;
        $this->document = $document;
        $this->indexer = $indexer;
        $this->resources = $resources;
    }

    /**
     * @param string|IndexableResource $resource
     */
    public function hasResource($resource): bool
    {
        if ($resource instanceof IndexableResource) {
            $resource = $resource->name;
        }

        return isset($this->resources[$resource]);
    }

    /**
     * @throws NonExistingResourceException if a resource with the given name doesn't exist on this index
     */
    public function getResource(string $name): IndexableResource
    {
        if (!$this->hasResource($name)) {
            throw NonExistingResourceException::fromNameAndIndex($name, $this->name, array_keys($this->resources));
        }

        return $this->resources[$name];
    }

    /**
     * Returns true if any of the resources configured on this index is an instance of the given class
     *
     * @param class-string $class
     */
    public function hasResourceWithClass(string $class): bool
    {
        foreach ($this->resources as $resource) {
            if ($resource->is($class)) {
                return true;
            }
        }

        return false;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
