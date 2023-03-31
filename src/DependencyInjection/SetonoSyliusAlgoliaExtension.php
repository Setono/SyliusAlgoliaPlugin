<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DependencyInjection;

use Setono\SyliusAlgoliaPlugin\DataMapper\DataMapperInterface;
use Setono\SyliusAlgoliaPlugin\Filter\Doctrine\FilterInterface as DoctrineFilterInterface;
use Setono\SyliusAlgoliaPlugin\Filter\Object\FilterInterface as ObjectFilterInterface;
use Setono\SyliusAlgoliaPlugin\Indexer\IndexerInterface;
use Setono\SyliusAlgoliaPlugin\Provider\IndexScope\IndexScopeProviderInterface;
use Setono\SyliusAlgoliaPlugin\UrlGenerator\ResourceUrlGeneratorInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SetonoSyliusAlgoliaExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        /**
         * @psalm-suppress PossiblyNullArgument
         *
         * @var array{
         *      indexes: array<string, mixed>,
         *      credentials: array{ app_id: string, search_only_api_key: string, admin_api_key: string },
         *      search: array{ enabled: bool, indexes: list<string> },
         *      index_name_prefix: string,
         *      routes: array{ product_index: string },
         *      cache: array{ adapter: string, enabled: bool, ttl: int }
         * } $config
         */
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        // indexes
        $container->setParameter('setono_sylius_algolia.indexes', $config['indexes']);

        // credentials
        $container->setParameter('setono_sylius_algolia.credentials.app_id', $config['credentials']['app_id']);
        $container->setParameter('setono_sylius_algolia.credentials.search_only_api_key', $config['credentials']['search_only_api_key']);
        $container->setParameter('setono_sylius_algolia.credentials.admin_api_key', $config['credentials']['admin_api_key']);

        // search
        if (true === $config['search']['enabled'] && [] === $config['search']['indexes']) {
            throw new \RuntimeException('When you enable search you need to provide at least one index to search');
        }
        foreach ($config['search']['indexes'] as $index) {
            if (!isset($config['indexes'][$index])) {
                throw new \RuntimeException(sprintf('For the search configuration you have added the index "%s". That index is not configured in setono_sylius_algolia.indexes.', $index));
            }
        }
        $container->setParameter('setono_sylius_algolia.search.enabled', $config['search']['enabled']);
        $container->setParameter('setono_sylius_algolia.search.indexes', $config['search']['indexes']);

        // cache
        $container->setParameter('setono_sylius_algolia.cache.adapter', $config['cache']['adapter']);
        $container->setParameter('setono_sylius_algolia.cache.ttl', $config['cache']['ttl']);

        // routes
        $container->setParameter('setono_sylius_algolia.routes.product_index', $config['routes']['product_index']);

        // misc
        $container->setParameter('setono_sylius_algolia.index_name_prefix', $config['index_name_prefix']);

        $loader->load('services.xml');

        if (true === $config['cache']['enabled']) {
            $loader->load('services/conditional/renderer.xml');
        }

        // auto configuration
        $container->registerForAutoconfiguration(DataMapperInterface::class)
            ->addTag('setono_sylius_algolia.data_mapper');

        $container->registerForAutoconfiguration(DoctrineFilterInterface::class)
            ->addTag('setono_sylius_algolia.doctrine_filter');

        $container->registerForAutoconfiguration(IndexScopeProviderInterface::class)
            ->addTag('setono_sylius_algolia.index_scope_provider');

        $container->registerForAutoconfiguration(ObjectFilterInterface::class)
            ->addTag('setono_sylius_algolia.object_filter');

        $container->registerForAutoconfiguration(ResourceUrlGeneratorInterface::class)
            ->addTag('setono_sylius_algolia.url_generator');

        $container->registerForAutoconfiguration(IndexerInterface::class)
            ->addTag('setono_sylius_algolia.indexer');
    }
}
