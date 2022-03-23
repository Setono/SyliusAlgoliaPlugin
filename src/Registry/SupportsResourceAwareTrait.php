<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Registry;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Sylius\Component\Resource\Model\ResourceInterface;

trait SupportsResourceAwareTrait
{
    /** @var class-string<ResourceInterface> */
    protected string $supports;

    /**
     * @param ResourceInterface|IndexableResource $resource
     */
    public function supports($resource): bool
    {
        $class = $resource instanceof IndexableResource ? $resource->className : get_class($resource);

        return is_a($class, $this->supports, true);
    }
}
