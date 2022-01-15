<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Handler;

use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use Setono\SyliusAlgoliaPlugin\DataMapper\DataMapperInterface;
use Setono\SyliusAlgoliaPlugin\Document\Product;
use Setono\SyliusAlgoliaPlugin\DTO\IndexSettings;
use Setono\SyliusAlgoliaPlugin\DTO\ProductIndexScope;
use Setono\SyliusAlgoliaPlugin\IndexResolver\ProductIndexNameResolverInterface;
use Setono\SyliusAlgoliaPlugin\Message\Command\PopulateProductIndex;
use Setono\SyliusAlgoliaPlugin\Repository\ProductRepositoryInterface;
use Setono\SyliusAlgoliaPlugin\SettingsProvider\SettingsProviderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class PopulateProductIndexHandler implements MessageHandlerInterface
{
    private ProductRepositoryInterface $productRepository;

    private ProductIndexNameResolverInterface $productIndexNameResolver;

    private SearchClient $searchClient;

    private NormalizerInterface $normalizer;

    private DataMapperInterface $dataMapper;

    private SettingsProviderInterface $defaultSettingsProvider;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductIndexNameResolverInterface $productIndexNameResolver,
        SearchClient $searchClient,
        NormalizerInterface $normalizer,
        DataMapperInterface $dataMapper,
        SettingsProviderInterface $defaultSettingsProvider
    ) {
        $this->productRepository = $productRepository;
        $this->productIndexNameResolver = $productIndexNameResolver;
        $this->searchClient = $searchClient;
        $this->normalizer = $normalizer;
        $this->dataMapper = $dataMapper;
        $this->defaultSettingsProvider = $defaultSettingsProvider;
    }

    public function __invoke(PopulateProductIndex $message): void
    {
        $scope = $message->getProductIndexScope();
        $index = $this->prepareIndex($scope);

        foreach ($this->getProducts($scope) as $product) {
            $doc = new Product();
            $this->dataMapper->map($product, $doc, [
                'channel' => $scope->channelCode,
                'locale' => $scope->localeCode,
                'currency' => $scope->currencyCode,
            ]);

            // todo validate the Product document

            $data = $this->normalizer->normalize($doc, null, [
                'groups' => 'setono:sylius-algolia:document',
            ]);

            $index->saveObject($data);
        }
    }

    private function prepareIndex(ProductIndexScope $scope): SearchIndex
    {
        $indexName = $this->productIndexNameResolver->resolveFromScope($scope);

        /** @var SearchIndex $index */
        $index = $this->searchClient->initIndex($indexName);

        // if the index already exists we don't want to override any settings
        if ($index->exists()) {
            return $index;
        }

        $settings = $this->defaultSettingsProvider->getSettings();
        if ($settings instanceof IndexSettings) {
            $language = substr($scope->localeCode, 0, 2);
            $settings->queryLanguages = [$language];
            $settings->indexLanguages = [$language];
        }

        $index->setSettings($settings->toArray());

        return $index;
    }

    /**
     * @return iterable<ProductInterface>
     */
    private function getProducts(ProductIndexScope $scope): iterable
    {
        $firstResult = 0;
        $maxResults = 100;

        $qb = $this->productRepository->createQueryBuilderFromProductIndexScope($scope);
        $qb->setMaxResults($maxResults);

        do {
            $qb->setFirstResult($firstResult);

            $products = $qb->getQuery()->getResult();
            Assert::isArray($products);

            /** @var ProductInterface $product */
            foreach ($products as $product) {
                yield $product;
            }

            $firstResult += $maxResults;
            // todo clear entity manager
        } while ([] !== $products);
    }
}
