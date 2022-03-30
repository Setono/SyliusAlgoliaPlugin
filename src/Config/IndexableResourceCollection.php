<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Config;

final class IndexableResourceCollection implements \IteratorAggregate
{
    /** @var array<string, IndexableResource> */
    private array $resources = [];

    public function __construct(IndexableResource ...$resources)
    {
        foreach ($resources as $resource) {
            $this->add($resource);
        }
    }

    private function add(IndexableResource $resource): void
    {
        $this->resources[$resource->name] = $resource;
    }

    /**
     * Returns true if any indexable resource matches the given name
     *
     * @psalm-assert-if-true IndexableResource $this->resources[$name]
     */
    public function hasWithName(string $name): bool
    {
        return isset($this->resources[$name]);
    }

    public function getByName(string $name): IndexableResource
    {
        if (!$this->hasWithName($name)) {
            throw new \InvalidArgumentException('No indexable resource with the given name');
        }

        return $this->resources[$name];
    }

    /**
     * Returns true if any indexable resource is an instance of the given class
     *
     * @param class-string $class
     */
    public function hasWithClass(string $class): bool
    {
        foreach ($this->resources as $resource) {
            if (is_a($resource->className, $class, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param object|class-string $class
     */
    public function getByClass($class): IndexableResource
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        foreach ($this->resources as $resource) {
            if (is_a($resource->className, $class, true)) {
                return $resource;
            }
        }

        throw new \InvalidArgumentException(sprintf('No indexable resource is an instance of %s', $class));
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->resources);
    }
}
