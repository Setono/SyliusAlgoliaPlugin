<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Registry;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Sylius\Component\Resource\Model\ResourceInterface;

interface SupportsResourceAwareInterface
{
    /**
     * Returns true if the service supports the given resource
     *
     * @param ResourceInterface|IndexableResource $resource
     */
    public function supports($resource): bool;
}
