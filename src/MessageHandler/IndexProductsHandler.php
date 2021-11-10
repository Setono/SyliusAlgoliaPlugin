<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\MessageHandler;

use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusAlgoliaPlugin\DataMapper\DataMapperInterface;
use Setono\SyliusAlgoliaPlugin\Document\Product;
use Setono\SyliusAlgoliaPlugin\DTO\IndexSettings;
use Setono\SyliusAlgoliaPlugin\Message\IndexProducts;
use Setono\SyliusAlgoliaPlugin\SettingsProvider\SettingsProviderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class IndexProductsHandler
{
    private EntityManagerInterface $productManager;

    private SearchClient $searchClient;

    private NormalizerInterface $normalizer;

    private DataMapperInterface $dataMapper;

    private SettingsProviderInterface $defaultSettingsProvider;

    public function __construct(
        EntityManagerInterface $productManager,
        SearchClient $searchClient,
        NormalizerInterface $normalizer,
        DataMapperInterface $dataMapper,
        SettingsProviderInterface $defaultSettingsProvider
    ) {
        $this->productManager = $productManager;
        $this->searchClient = $searchClient;
        $this->normalizer = $normalizer;
        $this->dataMapper = $dataMapper;
        $this->defaultSettingsProvider = $defaultSettingsProvider;
    }

    public function __invoke(IndexProducts $indexProducts): void
    {
        $query = $this->productManager->createQuery($indexProducts->getProductsDql());
        /** @var ArrayCollection $parameters */
        $parameters = $indexProducts->getProductsQueryParameters();
        $query->setParameters($parameters);

        $products = $query->getResult();

        $localeCode = null;
        if (null !== $locale = $indexProducts->getLocale()) {
            $localeCode = $locale->getCode();
        }
        $index = $this->prepareIndex($indexProducts->getIndexName(), $localeCode);

        foreach ($products as $product) {
            $doc = new Product();
            $this->dataMapper->map($product, $doc, [
                'channel' => $indexProducts->getChannel(),
                'locale' => $indexProducts->getLocale(),
                'currency' => $indexProducts->getCurrency(),
            ]);

            $data = $this->normalizer->normalize($doc, null, [
                'groups' => 'setono:sylius-algolia:document',
            ]);

            $index->saveObject($data);
        }
    }

    private function prepareIndex(string $indexName, ?string $localeCode): SearchIndex
    {
        $index = $this->searchClient->initIndex($indexName);

        // if the index already exists we don't want to override any settings
        if ($index->exists()) {
            return $index;
        }

        $settings = $this->defaultSettingsProvider->getSettings();
        if ($settings instanceof IndexSettings) {
            $language = substr((string) $localeCode, 0, 2);
            $settings->queryLanguages = [$language];
            $settings->indexLanguages = [$language];
        }

        $index->setSettings($settings->toArray());

        return $index;
    }
}
