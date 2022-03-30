<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DependencyInjection;

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
         *      admin_api_key: string
         * } $config
         */
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container->setParameter('setono_sylius_algolia.app_id', $config['app_id']);
        $container->setParameter('setono_sylius_algolia.search_only_api_key', $config['search_only_api_key']);
        $container->setParameter('setono_sylius_algolia.admin_api_key', $config['admin_api_key']);
        $container->setParameter('setono_sylius_algolia.indexable_resources', $config['indexable_resources']);

        $loader->load('services.xml');
    }
}
