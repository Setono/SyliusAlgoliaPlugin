<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DependencyInjection;

use Setono\SyliusAlgoliaPlugin\DataMapper\DataMapperInterface;
use Setono\SyliusAlgoliaPlugin\Filter\Doctrine\FilterInterface as DoctrineFilterInterface;
use Setono\SyliusAlgoliaPlugin\Filter\Object\FilterInterface as ObjectFilterInterface;
use Setono\SyliusAlgoliaPlugin\Provider\IndexScope\IndexScopeProviderInterface;
use Setono\SyliusAlgoliaPlugin\Provider\IndexSettings\IndexSettingsProviderInterface;
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
         *      indexable_resources: list<string>,
         *      app_id: string,
         *      search_only_api_key: string,
         *      admin_api_key: string,
         *      cache: array{adapter: string, enabled: bool, ttl: int}
         * } $config
         */
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container->setParameter('setono_sylius_algolia.app_id', $config['app_id']);
        $container->setParameter('setono_sylius_algolia.search_only_api_key', $config['search_only_api_key']);
        $container->setParameter('setono_sylius_algolia.admin_api_key', $config['admin_api_key']);
        $container->setParameter('setono_sylius_algolia.indexable_resources', $config['indexable_resources']);

        $loader->load('services.xml');

        $container->setParameter('setono_sylius_algolia.cache.adapter', $config['cache']['adapter']);
        $container->setParameter('setono_sylius_algolia.cache.ttl', $config['cache']['ttl']);

        if ($config['cache']['enabled']) {
            $loader->load('services/conditional/renderer.xml');
        }

        // auto configuration
        $container->registerForAutoconfiguration(DataMapperInterface::class)
            ->addTag('setono_sylius_algolia.data_mapper');

        $container->registerForAutoconfiguration(DoctrineFilterInterface::class)
            ->addTag('setono_sylius_algolia.doctrine_filter');

        $container->registerForAutoconfiguration(IndexScopeProviderInterface::class)
            ->addTag('setono_sylius_algolia.index_scope_provider');

        $container->registerForAutoconfiguration(IndexSettingsProviderInterface::class)
            ->addTag('setono_sylius_algolia.index_settings_provider');

        $container->registerForAutoconfiguration(ObjectFilterInterface::class)
            ->addTag('setono_sylius_algolia.object_filter');

        $container->registerForAutoconfiguration(ResourceUrlGeneratorInterface::class)
            ->addTag('setono_sylius_algolia.url_generator');
    }
}
