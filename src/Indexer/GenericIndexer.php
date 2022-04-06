<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Indexer;

use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResourceCollection;
use Setono\SyliusAlgoliaPlugin\DataMapper\DataMapperInterface;
use Setono\SyliusAlgoliaPlugin\Document\DocumentInterface;
use Setono\SyliusAlgoliaPlugin\IndexNameResolver\IndexNameResolverInterface;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Message\Command\IndexMultipleResources;
use Setono\SyliusAlgoliaPlugin\Provider\IndexScope\IndexScopeProviderInterface;
use Setono\SyliusAlgoliaPlugin\Provider\IndexSettings\IndexSettingsProviderInterface;
use Setono\SyliusAlgoliaPlugin\Registry\ResourceBasedRegistryInterface;
use Setono\SyliusAlgoliaPlugin\Registry\SupportsResourceAwareTrait;
use Setono\SyliusAlgoliaPlugin\Repository\IndexableResourceRepositoryInterface;
use Setono\SyliusAlgoliaPlugin\Settings\SettingsInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmozart\Assert\Assert;

class GenericIndexer implements IndexerInterface
{
    use ORMManagerTrait;
    use SupportsResourceAwareTrait;

    private IndexScopeProviderInterface $indexScopeProvider;

    private IndexNameResolverInterface $indexNameResolver;

    /** @var ResourceBasedRegistryInterface<IndexSettingsProviderInterface> */
    private ResourceBasedRegistryInterface $indexSettingsProviderRegistry;

    protected DataMapperInterface $dataMapper;

    private MessageBusInterface $commandBus;

    private ValidatorInterface $validator;

    private NormalizerInterface $normalizer;

    private SearchClient $algoliaClient;

    private IndexableResourceCollection $indexableResourceCollection;

    /** @var class-string<ResourceInterface> */
    private string $supports;

    /** @var class-string<DocumentInterface> */
    private string $documentClass;

    /** @var list<string> */
    private array $validationGroups;

    /** @var list<string> */
    private array $normalizationGroups;

    /**
     * @param ResourceBasedRegistryInterface<IndexSettingsProviderInterface> $indexSettingsProviderRegistry
     * @param class-string<ResourceInterface> $supports
     * @param class-string<DocumentInterface> $documentClass
     * @param list<string> $validationGroups
     * @param list<string> $normalizationGroups
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        IndexScopeProviderInterface $indexScopeProviderRegistry,
        IndexNameResolverInterface $indexNameResolver,
        ResourceBasedRegistryInterface $indexSettingsProviderRegistry,
        DataMapperInterface $dataMapper,
        MessageBusInterface $commandBus,
        ValidatorInterface $validator,
        NormalizerInterface $normalizer,
        SearchClient $algoliaClient,
        IndexableResourceCollection $indexableResourceCollection,
        string $supports,
        string $documentClass,
        array $validationGroups = ['setono_sylius_algolia'],
        array $normalizationGroups = ['setono:sylius-algolia:document']
    ) {
        $this->managerRegistry = $managerRegistry;
        $this->indexScopeProvider = $indexScopeProviderRegistry;
        $this->indexNameResolver = $indexNameResolver;
        $this->indexSettingsProviderRegistry = $indexSettingsProviderRegistry;
        $this->dataMapper = $dataMapper;
        $this->commandBus = $commandBus;
        $this->validator = $validator;
        $this->normalizer = $normalizer;
        $this->algoliaClient = $algoliaClient;
        $this->indexableResourceCollection = $indexableResourceCollection;
        $this->supports = $supports;
        $this->documentClass = $documentClass;
        $this->validationGroups = $validationGroups;
        $this->normalizationGroups = $normalizationGroups;
    }

    public function indexResource(IndexableResource $resource): void
    {
        foreach ($this->getIdBatches($resource) as $ids) {
            $this->commandBus->dispatch(new IndexMultipleResources($resource, $ids));
        }
    }

    public function index(ResourceInterface $resource): void
    {
        $this->indexMultiple([$resource]);
    }

    public function indexMultiple(array $resources, IndexableResource $indexableResource = null): void
    {
        if ([] === $resources) {
            return;
        }

        [$resources, $indexableResource] = $this->processInput($resources, $indexableResource);

        /** @var IndexSettingsProviderInterface $indexSettingsProvider */
        $indexSettingsProvider = $this->indexSettingsProviderRegistry->get($indexableResource);

        // process input
        foreach ($this->indexScopeProvider->getAll($indexableResource) as $indexScope) {
            $index = $this->prepareIndex(
                $this->indexNameResolver->resolveFromIndexScope($indexScope),
                $indexSettingsProvider->getSettings($indexScope)
            );

            foreach ($this->getObjects($resources, $indexableResource->className, $indexScope) as $obj) {
                $doc = $this->createNewDocument();
                $this->dataMapper->map($obj, $doc, $indexScope);

                // todo handle errors
                //$constraintViolationList = $this->validator->validate($doc, null, $this->validationGroups);

                $data = $this->normalize($doc);

                $index->saveObject($data);
            }
        }
    }

    /**
     * @return \Generator<list<scalar>>
     */
    protected function getIdBatches(IndexableResource $resource): \Generator
    {
        $manager = $this->getManager($resource->className);

        /** @var IndexableResourceRepositoryInterface|ObjectRepository $repository */
        $repository = $manager->getRepository($resource->className);
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
     *
     * @return array<array-key, ResourceInterface>
     */
    private function getObjects(array $resources, string $resourceClass, IndexScope $indexScope): array
    {
        $manager = $this->getManager($resourceClass);

        /** @var IndexableResourceRepositoryInterface|ObjectRepository $repository */
        $repository = $manager->getRepository($resourceClass);
        Assert::isInstanceOf($repository, IndexableResourceRepositoryInterface::class, sprintf(
            'The repository for resource "%s" must implement the interface %s',
            $resourceClass,
            IndexableResourceRepositoryInterface::class
        ));

        return $repository->findFromIndexScopeAndIds($indexScope, $resources);
    }

    protected function normalize(DocumentInterface $document): array
    {
        $res = $this->normalizer->normalize($document, null, [
            'groups' => $this->normalizationGroups,
        ]);
        Assert::isArray($res);

        return $res;
    }

    private function prepareIndex(string $indexName, SettingsInterface $indexSettings): SearchIndex
    {
        $index = $this->algoliaClient->initIndex($indexName);
        Assert::isInstanceOf($index, SearchIndex::class);

        // if the index already exists we don't want to override any settings
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
    private function processInput(array $resources, ?IndexableResource $indexableResource): array
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

                $indexableResource = $this->indexableResourceCollection->getByClass($resource);
            }

            $id = $resource instanceof ResourceInterface ? $resource->getId() : $resource;
            Assert::scalar($id);

            $processed[] = $id;
        }

        return [$processed, $indexableResource];
    }

    protected function createNewDocument(): DocumentInterface
    {
        $obj = new $this->documentClass();
        Assert::isInstanceOf($obj, DocumentInterface::class);

        return $obj;
    }

    protected function getSupportingType(): string
    {
        return $this->supports;
    }
}
