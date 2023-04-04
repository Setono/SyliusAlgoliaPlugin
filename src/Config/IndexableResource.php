<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Config;

use Setono\SyliusAlgoliaPlugin\Model\IndexableInterface;
use Webmozart\Assert\Assert;

/**
 * This class represents a Sylius resource that is indexable
 */
final class IndexableResource
{
    /**
     * This is the name of the Sylius resource, e.g. 'sylius.product'
     */
    public string $name;

    /**
     * This is the FQCN for the resource
     *
     * @var class-string<IndexableInterface>
     */
    public string $class;

    /**
     * @param class-string<IndexableInterface> $class
     */
    public function __construct(string $name, string $class)
    {
        Assert::stringNotEmpty($name);

        if (!is_a($class, IndexableInterface::class, true)) {
            throw new \InvalidArgumentException(sprintf(
                'The document class %s MUST be an instance of %s',
                $class,
                IndexableInterface::class
            ));
        }

        $this->name = $name;
        $this->class = $class;
    }

    /**
     * Returns true if the resource's class is an instance of $class
     *
     * @param object|class-string $class
     */
    public function is($class): bool
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        return is_a($this->class, $class, true);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
