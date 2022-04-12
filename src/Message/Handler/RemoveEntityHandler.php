<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Handler;

use Doctrine\Persistence\ManagerRegistry;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusAlgoliaPlugin\Indexer\IndexerInterface;
use Setono\SyliusAlgoliaPlugin\Message\Command\RemoveEntity;
use Setono\SyliusAlgoliaPlugin\Registry\ResourceBasedRegistryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RemoveEntityHandler implements MessageHandlerInterface
{
    use ORMManagerTrait;

    /** @var ResourceBasedRegistryInterface<IndexerInterface> */
    private ResourceBasedRegistryInterface $indexerRegistry;

    /**
     * @param ResourceBasedRegistryInterface<IndexerInterface> $indexerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry, ResourceBasedRegistryInterface $indexerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
        $this->indexerRegistry = $indexerRegistry;
    }

    public function __invoke(RemoveEntity $message): void
    {
        $manager = $this->getManager($message->entityClass);
        $obj = $manager->find($message->entityClass, $message->entityId);

        if (!$obj instanceof ResourceInterface) {
            throw new UnrecoverableMessageHandlingException(sprintf(
                'The class %s does not implement %s',
                $message->entityClass,
                ResourceInterface::class
            ));
        }

        try {
            /** @var IndexerInterface $indexer */
            $indexer = $this->indexerRegistry->get($obj);
        } catch (\InvalidArgumentException $e) {
            throw new UnrecoverableMessageHandlingException($e->getMessage(), 0, $e);
        }

        $indexer->removeEntity($obj);
    }
}
