<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Command;

use Setono\SyliusAlgoliaPlugin\IndexResolver\ProductIndexNameResolverInterface;
use Setono\SyliusAlgoliaPlugin\IndexResolver\ProductIndexesResolverInterface;
use Setono\SyliusAlgoliaPlugin\Message\IndexProducts;
use Setono\SyliusAlgoliaPlugin\Repository\ProductRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class PopulateCommand extends Command
{
    protected static $defaultName = 'setono:sylius-algolia:populate';

    protected static $defaultDescription = 'Populate indexes';

    private ProductRepositoryInterface $productRepository;

    private ProductIndexNameResolverInterface $productIndexNameResolver;

    private ProductIndexesResolverInterface $productsIndicesResolver;

    private MessageBusInterface $messageBus;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductIndexNameResolverInterface $productIndexNameResolver,
        ProductIndexesResolverInterface $productsIndicesResolver,
        MessageBusInterface $messageBus
    ) {
        parent::__construct();

        $this->productRepository = $productRepository;
        $this->productIndexNameResolver = $productIndexNameResolver;
        $this->productsIndicesResolver = $productsIndicesResolver;
        $this->messageBus = $messageBus;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $productsIndices = $this->productsIndicesResolver->resolve();

        foreach ($productsIndices as $productIndex) {
            $qb = $this->productRepository->createQueryBuilderForResolvedIndex($productIndex);
            $indexName = $this->productIndexNameResolver->resolve(
                $productIndex->getChannelCode(),
                $productIndex->getLocaleCode(),
                $productIndex->getCurrencyCode()
            );

            $this->messageBus->dispatch(new IndexProducts(
                $indexName,
                $qb->getDQL(),
                $qb->getParameters(),
                $productIndex->getChannel(),
                $productIndex->getLocale(),
                $productIndex->getCurrency()
            ));
        }

//        /** @var ProductInterface $product */
//        foreach ($this->productRepository->findAll() as $product) {
//            /** @var ChannelInterface $channel */
//            foreach ($product->getChannels() as $channel) {
//                foreach ($channel->getLocales() as $locale) {
//                    foreach ($channel->getCurrencies() as $currency) {
//                        $index = $this->prepareIndex($channel, $locale, $currency);
//
//                        $doc = new Product();
//                        $this->dataMapper->map($product, $doc, [
//                            'channel' => $channel,
//                            'locale' => $locale,
//                            'currency' => $currency,
//                        ]);
//
//                        $data = $this->normalizer->normalize($doc, null, [
//                            'groups' => 'setono:sylius-algolia:document',
//                        ]);
//
//                        $index->saveObject($data);
//                    }
//                }
//            }
//        }

        return Command::SUCCESS;
    }
}
