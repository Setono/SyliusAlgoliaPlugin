<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Registry;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @template T
 */
interface ResourceBasedRegistryInterface
{
    /**
     * @param ResourceInterface|IndexableResource $resource
     *
     * @return T
     *
     * @throws \InvalidArgumentException if no service supports the resource
     */
    public function get($resource);
}
