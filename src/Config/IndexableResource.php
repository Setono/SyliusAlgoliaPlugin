<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Config;

use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

final class IndexableResource
{
    /**
     * This is the name of the resource, i.e. sylius.product
     */
    public string $name;

    /**
     * This is the short name of the resource, i.e. the part that comes after the last . (dot) in the name.
     * For sylius.product, this would be 'product'
     */
    public string $shortName;

    /**
     * This is the FQCN for the Sylius resource, i.e. for sylius.product this could be App\Entity\Product\Product
     *
     * @var class-string<ResourceInterface>
     */
    public string $className;

    /**
     * @param class-string<ResourceInterface> $className
     */
    public function __construct(string $name, string $className)
    {
        Assert::stringNotEmpty($name);
        if (!is_a($className, ResourceInterface::class, true)) {
            throw new \InvalidArgumentException(sprintf('The class %s MUST be an instance of %s', $className, ResourceInterface::class));
        }

        $this->name = $name;
        $this->shortName = $name;

        $pos = strrpos($name, '.');
        if (is_int($pos)) {
            $this->shortName = substr($name, $pos + 1);
        }

        $this->className = $className;
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->name, $this->className);
    }
}
