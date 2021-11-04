<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('setono_sylius_algolia');
        $rootNode = $treeBuilder->getRootNode();

        /**
         * @psalm-suppress MixedMethodCall
         * @psalm-suppress PossiblyUndefinedMethod
         * @psalm-suppress PossiblyNullReference
         */
        $rootNode
            ->children()
                ->scalarNode('app_id')
                    ->info('This is your unique application identifier. It\'s used to identify you when using Algolia\'s API.')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('search_only_api_key')
                    ->info('This is the public API key to use in your frontend code. This key is only usable for search queries and sending data to the Insights API.')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('admin_api_key')
                    ->info('This is the ADMIN API key. Please keep it secret and use it ONLY from your backend: this key is used to create, update and DELETE your indices. You can also use it to manage your API keys.')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
