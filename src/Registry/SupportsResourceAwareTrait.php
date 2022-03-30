<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Registry;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Sylius\Component\Resource\Model\ResourceInterface;

trait SupportsResourceAwareTrait
{
    /**
     * @param ResourceInterface|IndexableResource $resource
     */
    public function supports($resource): bool
    {
        $class = $resource instanceof IndexableResource ? $resource->className : get_class($resource);

        return is_a($class, $this->getSupportingType(), true);
    }

    /**
     * Returns the class string that this service supports
     *
     * @return class-string<ResourceInterface>
     */
    abstract protected function getSupportingType(): string;
}
