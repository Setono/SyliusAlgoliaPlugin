<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Indexer;

use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\Config\IndexRegistry;
use Setono\SyliusAlgoliaPlugin\DataMapper\DataMapperInterface;
use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\Filter\Doctrine\FilterInterface as DoctrineFilterInterface;
use Setono\SyliusAlgoliaPlugin\Filter\Object\FilterInterface as ObjectFilterInterface;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Message\Command\IndexEntities;
use Setono\SyliusAlgoliaPlugin\Message\Command\IndexResource;
use Setono\SyliusAlgoliaPlugin\Model\IndexableInterface;
use Setono\SyliusAlgoliaPlugin\Provider\IndexScope\IndexScopeProviderInterface;
use Setono\SyliusAlgoliaPlugin\Provider\IndexSettings\IndexSettingsProviderInterface;
use Setono\SyliusAlgoliaPlugin\Repository\IndexableResourceRepositoryInterface;
use Setono\SyliusAlgoliaPlugin\Resolver\IndexName\IndexNameResolverInterface;
use Setono\SyliusAlgoliaPlugin\Settings\IndexSettings;
use Setono\SyliusAlgoliaPlugin\Settings\SortableReplica;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

/**
 * NOT final as this makes it easier to override and extend this indexer
 */
class DefaultIndexer extends AbstractIndexer
{
    use ORMManagerTrait;

    protected IndexScopeProviderInterface $indexScopeProvider;

    protected IndexNameResolverInterface $indexNameResolver;

    protected IndexSettingsProviderInterface $indexSettingsProvider;

    protected DataMapperInterface $dataMapper;

    protected MessageBusInterface $commandBus;

    protected NormalizerInterface $normalizer;

    protected SearchClient $algoliaClient;

    protected IndexRegistry $indexRegistry;

    protected DoctrineFilterInterface $doctrineFilter;

    protected ObjectFilterInterface $objectFilter;

    /** @var list<string> */
    protected array $normalizationGroups;

    /**
     * @param list<string> $normalizationGroups
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        IndexScopeProviderInterface $indexScopeProvider,
        IndexNameResolverInterface $indexNameResolver,
        IndexSettingsProviderInterface $indexSettingsProvider,
        DataMapperInterface $dataMapper,
        MessageBusInterface $commandBus,
        NormalizerInterface $normalizer,
        SearchClient $algoliaClient,
        IndexRegistry $indexRegistry,
        DoctrineFilterInterface $doctrineFilter,
        ObjectFilterInterface $objectFilter,
        array $normalizationGroups = ['setono:sylius-algolia:document']
    ) {
        $this->managerRegistry = $managerRegistry;
        $this->indexScopeProvider = $indexScopeProvider;
        $this->indexNameResolver = $indexNameResolver;
        $this->indexSettingsProvider = $indexSettingsProvider;
        $this->dataMapper = $dataMapper;
        $this->commandBus = $commandBus;
        $this->normalizer = $normalizer;
        $this->algoliaClient = $algoliaClient;
        $this->indexRegistry = $indexRegistry;
        $this->doctrineFilter = $doctrineFilter;
        $this->objectFilter = $objectFilter;
        $this->normalizationGroups = $normalizationGroups;
    }

    public function index($index): void
    {
        if (is_string($index)) {
            $index = $this->indexRegistry->getByName($index);
        }

        foreach ($index->resources as $resource) {
            $this->commandBus->dispatch(new IndexResource($index, $resource));
        }
    }

    public function indexResource($index, string $resource): void
    {
        if (is_string($index)) {
            $index = $this->indexRegistry->getByName($index);
        }

        $indexableResource = $index->getResource($resource);

        foreach ($this->getIdBatches($indexableResource) as $ids) {
            $this->commandBus->dispatch(new IndexEntities($indexableResource, $ids));
        }
    }

    public function indexEntitiesWithIds(array $ids, string $type): void
    {
        if ([] === $ids) {
            return;
        }

        $index = $this->indexRegistry->getByResourceClass($type);

        foreach ($this->indexScopeProvider->getAll($index) as $indexScope) {
            $algoliaIndex = $this->prepareIndex(
                $this->indexNameResolver->resolveFromIndexScope($indexScope),
                $this->indexSettingsProvider->getSettings($indexScope)
            );

            foreach ($this->getObjects($ids, $type, $indexScope) as $obj) {
                $doc = new $index->document();
                $this->dataMapper->map($obj, $doc, $indexScope);

                $this->objectFilter->filter($obj, $doc, $indexScope);

                $data = $this->normalize($doc);

                $algoliaIndex->saveObject($data);
            }
        }
    }

    public function removeEntitiesWithIds(array $ids, string $type): void
    {
        if ([] === $ids) {
            return;
        }

        $index = $this->indexRegistry->getByResourceClass($type);

        foreach ($this->indexScopeProvider->getAll($index) as $indexScope) {
            $algoliaIndex = $this->prepareIndex(
                $this->indexNameResolver->resolveFromIndexScope($indexScope),
                $this->indexSettingsProvider->getSettings($indexScope)
            );

            foreach ($this->getObjects($ids, $type, $indexScope) as $obj) {
                $algoliaIndex->deleteObject($obj->getObjectId());
            }
        }
    }

    /**
     * @return \Generator<int, non-empty-list<mixed>>
     */
    protected function getIdBatches(IndexableResource $resource): \Generator
    {
        $manager = $this->getManager($resource->class);

        /** @var IndexableResourceRepositoryInterface|ObjectRepository $repository */
        $repository = $manager->getRepository($resource->class);
        Assert::isInstanceOf($repository, IndexableResourceRepositoryInterface::class, sprintf(
            'The repository for resource "%s" must implement the interface %s',
            $resource->name,
            IndexableResourceRepositoryInterface::class
        ));

        $firstResult = 0;
        $maxResults = 100;

        $qb = $repository->createIndexableCollectionQueryBuilder();
        $qb->select(sprintf('%s.id', $qb->getRootAliases()[0]));
        $qb->setMaxResults($maxResults);

        $this->doctrineFilter->apply($qb, $resource);

        while (true) {
            $qb->setFirstResult($firstResult);

            $ids = $qb->getQuery()->getResult();
            Assert::isArray($ids);

            $ids = array_values(array_map(/** @return mixed */static function (array $elm) {
                Assert::keyExists($elm, 'id');

                return $elm['id'];
            }, $ids));

            if ([] === $ids) {
                break;
            }

            yield $ids;

            $firstResult += $maxResults;

            $manager->clear();
        }
    }

    /**
     * @param list<mixed> $ids
     * @param class-string<IndexableInterface> $type
     *
     * @return list<IndexableInterface>
     */
    protected function getObjects(array $ids, string $type, IndexScope $indexScope): array
    {
        $manager = $this->getManager($type);

        /** @var IndexableResourceRepositoryInterface|ObjectRepository $repository */
        $repository = $manager->getRepository($type);
        Assert::isInstanceOf($repository, IndexableResourceRepositoryInterface::class, sprintf(
            'The repository for resource "%s" must implement the interface %s',
            $type,
            IndexableResourceRepositoryInterface::class
        ));

        return $repository->findFromIndexScopeAndIds($indexScope, $ids);
    }

    protected function normalize(Document $document): array
    {
        $res = $this->normalizer->normalize($document, null, [
            'groups' => $this->normalizationGroups,
        ]);
        Assert::isArray($res);

        return $res;
    }

    protected function prepareIndex(string $indexName, IndexSettings $indexSettings): SearchIndex
    {
        $index = $this->algoliaClient->initIndex($indexName);
        Assert::isInstanceOf($index, SearchIndex::class);

        // if the index already exists we don't want to override any settings. TODO why don't we want that? Should we make a command that resets settings to application defaults? We also need to take into account the forwardToReplicas option below
        if ($index->exists()) {
            return $index;
        }

        /**
         * this first call will create the index (including any replica indexes)
         *
         * @psalm-suppress MixedMethodCall
         */
        $index->setSettings($indexSettings->toArray())->wait();

        /** @psalm-suppress MixedAssignment,MixedArrayAccess */
        $indexSettings->ranking = $index->getSettings()['ranking'];

        foreach ($indexSettings->replicas as $replica) {
            if (!$replica instanceof SortableReplica) {
                continue;
            }

            $replicaIndex = $this->algoliaClient->initIndex($replica->name);
            Assert::isInstanceOf($replicaIndex, SearchIndex::class);

            $replicaIndexSettings = clone $indexSettings;
            $replicaIndexSettings->replicas = [];
            array_unshift($replicaIndexSettings->ranking, $replica->ranking());

            $replicaIndex->setSettings($replicaIndexSettings->toArray());
        }

        return $index;
    }

    public function supports($value): bool
    {
        return true;
    }
}
