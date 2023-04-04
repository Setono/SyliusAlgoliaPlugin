<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Config;

use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\Exception\NonExistingIndexException;
use Setono\SyliusAlgoliaPlugin\Model\IndexableInterface;

/**
 * @implements \IteratorAggregate<string, Index>
 */
final class IndexRegistry implements \IteratorAggregate
{
    /**
     * An array of indexes, indexed by the name of the index
     *
     * @var array<string, Index>
     */
    private array $indexes = [];

    /**
     * This method creates the index registry based on the configuration of the plugin
     *
     * @internal
     *
     * @param array<string, array{document: class-string<Document>, resources: list<string>}> $indexes
     * @param array<string, array{classes: array{model: class-string<IndexableInterface>}}> $resources This is the array of defined Sylius resources
     */
    public static function fromConfiguration(array $indexes, array $resources): self
    {
        $registry = new self();

        foreach ($indexes as $indexName => $index) {
            $configuredResources = [];

            foreach ($index['resources'] as $configuredResource) {
                if (!isset($resources[$configuredResource])) {
                    throw new \InvalidArgumentException(sprintf(
                        'The resource %s is not a valid Sylius resource. Valid resources are [%s]',
                        $configuredResource,
                        implode(', ', array_keys($resources))
                    ));
                }

                $configuredResources[$configuredResource] = new IndexableResource(
                    $configuredResource,
                    $resources[$configuredResource]['classes']['model']
                );
            }

            $registry->add(new Index($indexName, $index['document'], $configuredResources));
        }

        return $registry;
    }

    /**
     * @throws \InvalidArgumentException if one of the resources on the $index has already been configured on another index
     */
    public function add(Index $index): void
    {
        foreach ($this->indexes as $existingIndex) {
            foreach ($index->resources as $resource) {
                if ($existingIndex->hasResource($resource)) {
                    throw new \InvalidArgumentException(sprintf(
                        'The resource "%s" is already defined on the index "%s"',
                        $resource->name,
                        $existingIndex->name
                    ));
                }
            }
        }

        $this->indexes[$index->name] = $index;
    }

    /**
     * @throws NonExistingIndexException if no index exists with the given name
     */
    public function getByName(string $name): Index
    {
        if (!isset($this->indexes[$name])) {
            throw NonExistingIndexException::fromName($name, array_keys($this->indexes));
        }

        return $this->indexes[$name];
    }

    /**
     * This method returns the index where the $class is configured
     *
     * @param object|class-string $class
     *
     * @throws \InvalidArgumentException if the given resource is not configured on any index
     */
    public function getByResourceClass($class): Index
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        foreach ($this->indexes as $index) {
            // todo the old if here was
            // if (is_a($resource->resourceClass, $class, true) || is_a($class, $resource->resourceClass, true)) {
            // what was the reason for that?
            if ($index->hasResourceWithClass($class)) {
                return $index;
            }
        }

        throw new \InvalidArgumentException(sprintf('No index exists having a resource that is an instance of %s', $class));
    }

    /**
     * @return \ArrayIterator<string, Index>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->indexes);
    }
}
