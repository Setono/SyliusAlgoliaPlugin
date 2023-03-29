<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Indexer;

use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResourceRegistry;
use Setono\SyliusAlgoliaPlugin\DataMapper\DataMapperInterface;
use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\Filter\Doctrine\FilterInterface as DoctrineFilterInterface;
use Setono\SyliusAlgoliaPlugin\Filter\Object\FilterInterface as ObjectFilterInterface;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Message\Command\IndexEntities;
use Setono\SyliusAlgoliaPlugin\Model\IndexableInterface;
use Setono\SyliusAlgoliaPlugin\Provider\IndexScope\IndexScopeProviderInterface;
use Setono\SyliusAlgoliaPlugin\Provider\IndexSettings\IndexSettingsProviderInterface;
use Setono\SyliusAlgoliaPlugin\Registry\SupportsResourceAwareTrait;
use Setono\SyliusAlgoliaPlugin\Repository\IndexableResourceRepositoryInterface;
use Setono\SyliusAlgoliaPlugin\Resolver\IndexNameResolverInterface;
use Setono\SyliusAlgoliaPlugin\Settings\SettingsInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

class GenericIndexer implements IndexerInterface
{
    use ORMManagerTrait;

    use SupportsResourceAwareTrait;

    protected IndexScopeProviderInterface $indexScopeProvider;

    protected IndexNameResolverInterface $indexNameResolver;

    protected IndexSettingsProviderInterface $indexSettingsProvider;

    protected DataMapperInterface $dataMapper;

    protected MessageBusInterface $commandBus;

    protected NormalizerInterface $normalizer;

    protected SearchClient $algoliaClient;

    protected IndexableResourceRegistry $indexableResourceRegistry;

    protected DoctrineFilterInterface $doctrineFilter;

    protected ObjectFilterInterface $objectFilter;

    /** @var class-string<ResourceInterface> */
    protected string $supports;

    /** @var list<string> */
    protected array $normalizationGroups;

    /**
     * @param class-string<ResourceInterface> $supports
     * @param list<string> $normalizationGroups
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        IndexScopeProviderInterface $indexScopeProviderRegistry,
        IndexNameResolverInterface $indexNameResolver,
        IndexSettingsProviderInterface $indexSettingsProvider,
        DataMapperInterface $dataMapper,
        MessageBusInterface $commandBus,
        NormalizerInterface $normalizer,
        SearchClient $algoliaClient,
        IndexableResourceRegistry $indexableResourceRegistry,
        DoctrineFilterInterface $doctrineFilter,
        ObjectFilterInterface $objectFilter,
        string $supports,
        array $normalizationGroups = ['setono:sylius-algolia:document']
    ) {
        $this->managerRegistry = $managerRegistry;
        $this->indexScopeProvider = $indexScopeProviderRegistry;
        $this->indexNameResolver = $indexNameResolver;
        $this->indexSettingsProvider = $indexSettingsProvider;
        $this->dataMapper = $dataMapper;
        $this->commandBus = $commandBus;
        $this->normalizer = $normalizer;
        $this->algoliaClient = $algoliaClient;
        $this->indexableResourceRegistry = $indexableResourceRegistry;
        $this->doctrineFilter = $doctrineFilter;
        $this->objectFilter = $objectFilter;
        $this->supports = $supports;
        $this->normalizationGroups = $normalizationGroups;
    }

    public function indexResource(IndexableResource $resource): void
    {
        foreach ($this->getIdBatches($resource) as $ids) {
            $this->commandBus->dispatch(new IndexEntities($resource, $ids));
        }
    }

    public function indexEntity(ResourceInterface $entity): void
    {
        $this->indexEntities([$entity]);
    }

    public function indexEntities(array $entities, IndexableResource $indexableResource = null): void
    {
        if ([] === $entities) {
            return;
        }

        [$entities, $indexableResource] = $this->processInput($entities, $indexableResource);

        // process input
        foreach ($this->indexScopeProvider->getAll($indexableResource) as $indexScope) {
            $index = $this->prepareIndex(
                $this->indexNameResolver->resolveFromIndexScope($indexScope),
                $this->indexSettingsProvider->getSettings($indexScope)
            );

            foreach ($this->getObjects($entities, $indexableResource->resourceClass, $indexScope) as $obj) {
                $doc = new $indexableResource->documentClass();
                $this->dataMapper->map($obj, $doc, $indexScope);

                $this->objectFilter->filter($obj, $doc, $indexScope);

                $data = $this->normalize($doc);

                $index->saveObject($data);
            }
        }
    }

    public function removeEntity(ResourceInterface $entity): void
    {
        $this->removeEntities([$entity]);
    }

    public function removeEntities(array $entities, IndexableResource $indexableResource = null): void
    {
        if ([] === $entities) {
            return;
        }

        [$entities, $indexableResource] = $this->processInput($entities, $indexableResource);

        // process input
        foreach ($this->indexScopeProvider->getAll($indexableResource) as $indexScope) {
            $index = $this->prepareIndex(
                $this->indexNameResolver->resolveFromIndexScope($indexScope),
                $this->indexSettingsProvider->getSettings($indexScope)
            );

            foreach ($this->getObjects($entities, $indexableResource->resourceClass, $indexScope) as $obj) {
                $index->deleteObject($obj->getObjectId());
            }
        }
    }

    /**
     * @return \Generator<list<scalar>>
     */
    protected function getIdBatches(IndexableResource $indexableResource): \Generator
    {
        $manager = $this->getManager($indexableResource->resourceClass);

        /** @var IndexableResourceRepositoryInterface|ObjectRepository $repository */
        $repository = $manager->getRepository($indexableResource->resourceClass);
        Assert::isInstanceOf($repository, IndexableResourceRepositoryInterface::class, sprintf(
            'The repository for resource "%s" must implement the interface %s',
            $indexableResource->name,
            IndexableResourceRepositoryInterface::class
        ));

        $firstResult = 0;
        $maxResults = 100;

        $qb = $repository->createIndexableCollectionQueryBuilder();
        $qb->select(sprintf('%s.id', $qb->getRootAliases()[0]));
        $qb->setMaxResults($maxResults);

        $this->doctrineFilter->apply($qb, $indexableResource);

        do {
            $qb->setFirstResult($firstResult);

            $ids = $qb->getQuery()->getResult();
            Assert::isArray($ids);

            /**
             * @var list<scalar> $ids
             *
             * @psalm-suppress MissingClosureReturnType
             */
            $ids = array_map(static function (array $elm) {
                Assert::keyExists($elm, 'id');

                return $elm['id'];
            }, $ids);

            yield $ids;

            $firstResult += $maxResults;

            $manager->clear();
        } while ([] !== $ids);
    }

    /**
     * @param list<scalar> $resources
     * @param class-string<ResourceInterface> $resourceClass
     * @psalm-suppress InvalidReturnType,MoreSpecificReturnType
     *
     * @return list<IndexableInterface>
     */
    protected function getObjects(array $resources, string $resourceClass, IndexScope $indexScope): array
    {
        $manager = $this->getManager($resourceClass);

        /** @var IndexableResourceRepositoryInterface|ObjectRepository $repository */
        $repository = $manager->getRepository($resourceClass);
        Assert::isInstanceOf($repository, IndexableResourceRepositoryInterface::class, sprintf(
            'The repository for resource "%s" must implement the interface %s',
            $resourceClass,
            IndexableResourceRepositoryInterface::class
        ));

        /** @psalm-suppress InvalidReturnStatement,LessSpecificReturnStatement */
        return $repository->findFromIndexScopeAndIds($indexScope, $resources);
    }

    protected function normalize(Document $document): array
    {
        $res = $this->normalizer->normalize($document, null, [
            'groups' => $this->normalizationGroups,
        ]);
        Assert::isArray($res);

        return $res;
    }

    protected function prepareIndex(string $indexName, SettingsInterface $indexSettings): SearchIndex
    {
        $index = $this->algoliaClient->initIndex($indexName);
        Assert::isInstanceOf($index, SearchIndex::class);

        // if the index already exists we don't want to override any settings. TODO why don't we want that? Should we make a command that resets settings to application defaults?
        if ($index->exists()) {
            return $index;
        }

        $index->setSettings($indexSettings->toArray());

        return $index;
    }

    /**
     * @param non-empty-list<scalar|ResourceInterface> $resources
     *
     * @return array{0: non-empty-list<scalar>, 1: IndexableResource}
     */
    protected function processInput(array $resources, ?IndexableResource $indexableResource): array
    {
        $processed = [];
        foreach ($resources as $resource) {
            if (!is_scalar($resource) && !$resource instanceof ResourceInterface) {
                throw new \InvalidArgumentException(sprintf(
                    'The $resources array must be scalars or instances of %s',
                    ResourceInterface::class
                ));
            }

            if (null === $indexableResource) {
                if (is_scalar($resource)) {
                    throw new \InvalidArgumentException('When the $resources array are scalars, the $indexableResource must be set');
                }

                $indexableResource = $this->indexableResourceRegistry->getByClass($resource);
            }

            $id = $resource instanceof ResourceInterface ? $resource->getId() : $resource;
            Assert::scalar($id);

            $processed[] = $id;
        }

        return [$processed, $indexableResource];
    }

    protected function getSupportingType(): string
    {
        return $this->supports;
    }
}
