<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Registry;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Webmozart\Assert\Assert;

/**
 * @template T of SupportsResourceAwareInterface
 * @implements ResourceBasedRegistryInterface<T>
 */
final class ResourceBasedRegistry implements ResourceBasedRegistryInterface
{
    /** @var class-string<T> */
    private string $type;

    /** @var array<int, non-empty-list<T>> */
    private array $services = [];

    /**
     * @param class-string<T> $type
     */
    public function __construct(string $type)
    {
        if (!interface_exists($type) && !class_exists($type)) {
            throw new \InvalidArgumentException(sprintf('The $type "%s" does not exist', $type));
        }

        $this->type = $type;
    }

    /**
     * @param T $service
     */
    public function register(object $service, int $priority = 0): void
    {
        Assert::isInstanceOf($service, $this->type);

        $services = $this->services[$priority] ?? [];
        $services[] = $service;

        $this->services[$priority] = $services;
    }

    public function get($resource)
    {
        foreach ($this->iterate() as $service) {
            if ($service->supports($resource)) {
                return $service;
            }
        }

        throw new \InvalidArgumentException(sprintf(
            'No service supports the given resource, "%s"',
            $resource instanceof IndexableResource ? $resource->name : get_class($resource)
        ));
    }

    /**
     * @return iterable<SupportsResourceAwareInterface>
     *
     * @psalm-return \Generator<T>
     */
    private function iterate(): \Generator
    {
        $priorities = array_keys($this->services);
        rsort($priorities, \SORT_NUMERIC);

        foreach ($priorities as $priority) {
            foreach ($this->services[$priority] as $service) {
                yield $service;
            }
        }
    }
}
