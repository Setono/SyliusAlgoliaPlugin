<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Command;

use Sylius\Component\Resource\Model\ResourceInterface;

abstract class EntityAwareCommand implements CommandInterface
{
    /** @var class-string<ResourceInterface> */
    public string $entityClass;

    /** @var mixed */
    public $entityId;

    public function __construct(ResourceInterface $resource)
    {
        $this->entityClass = get_class($resource);
        $this->entityId = $resource->getId();
    }
}
