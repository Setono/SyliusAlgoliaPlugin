<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Command;

use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use Setono\SyliusAlgoliaPlugin\DataMapper\DataMapperInterface;
use Setono\SyliusAlgoliaPlugin\Document\Product;
use Setono\SyliusAlgoliaPlugin\IndexResolver\ProductIndexResolverInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Repository\ProductRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class PopulateCommand extends Command
{
    protected static $defaultName = 'setono:sylius-algolia:populate';

    protected static $defaultDescription = 'Populate indexes';

    private ProductRepositoryInterface $productRepository;

    private SearchClient $searchClient;

    private NormalizerInterface $normalizer;

    private DataMapperInterface $dataMapper;

    private ProductIndexResolverInterface $productIndexResolver;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchClient $searchClient,
        NormalizerInterface $normalizer,
        DataMapperInterface $dataMapper,
        ProductIndexResolverInterface $productIndexResolver
    ) {
        parent::__construct();

        $this->productRepository = $productRepository;
        $this->searchClient = $searchClient;
        $this->normalizer = $normalizer;
        $this->dataMapper = $dataMapper;
        $this->productIndexResolver = $productIndexResolver;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var ProductInterface $product */
        foreach ($this->productRepository->findAll() as $product) {
            /** @var ChannelInterface $channel */
            foreach ($product->getChannels() as $channel) {
                foreach ($channel->getLocales() as $locale) {
                    /** @var SearchIndex $index */
                    $index = $this->searchClient->initIndex($this->productIndexResolver->resolve($channel, $locale));

                    $doc = new Product();
                    $this->dataMapper->map($product, $doc, [
                        'channel' => $channel,
                        'locale' => $locale,
                    ]);

                    $data = $this->normalizer->normalize($doc, null, [
                        'groups' => 'setono:sylius-algolia:document',
                    ]);

                    $index->saveObject($data);
                }
            }
        }

        return Command::SUCCESS;
    }
}
