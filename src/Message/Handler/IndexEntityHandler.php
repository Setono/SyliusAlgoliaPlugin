<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Handler;

use Doctrine\Persistence\ManagerRegistry;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusAlgoliaPlugin\Indexer\IndexerInterface;
use Setono\SyliusAlgoliaPlugin\Message\Command\RemoveEntity;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class IndexEntityHandler implements MessageHandlerInterface
{
    use ORMManagerTrait;

    private IndexerInterface $indexer;

    public function __construct(ManagerRegistry $managerRegistry, IndexerInterface $indexer)
    {
        $this->managerRegistry = $managerRegistry;
        $this->indexer = $indexer;
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
            $this->indexer->indexEntity($obj);
        } catch (\InvalidArgumentException $e) {
            throw new UnrecoverableMessageHandlingException($e->getMessage(), 0, $e);
        }
    }
}
