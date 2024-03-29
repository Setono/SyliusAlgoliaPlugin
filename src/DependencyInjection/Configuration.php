<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DependencyInjection;

use Setono\SyliusAlgoliaPlugin\Document\Product;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('setono_sylius_algolia');
        $rootNode = $treeBuilder->getRootNode();

        /** @psalm-suppress MixedMethodCall,UndefinedMethod,PossiblyUndefinedMethod,PossiblyNullReference */
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('indexes')
                    ->info('Define the indexes you want to create. All based on a document class you define')
                    ->useAttributeAsKey('name')
                    ->beforeNormalization()->castToArray()->end()
                    ->defaultValue([])
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('document')
                                ->info(sprintf('The fully qualified class name for the document that maps to the index. If you are creating a product index, a good starting point is the %s', Product::class))
                                ->cannotBeEmpty()
                                ->isRequired()
                            ->end()
                            ->scalarNode('indexer')
                                ->info('This is the service id of the indexer that will be used to index resources on this index')
                                ->cannotBeEmpty()
                                ->defaultValue('setono_sylius_algolia.indexer.default')
                            ->end()
                            ->arrayNode('resources')
                                ->info('The Sylius resources that make up this index. Examples could be "sylius.product", "sylius.taxon", etc.')
                                ->scalarPrototype()->end()
                            ->end()
                            ->scalarNode('prefix')
                                ->defaultNull()
                                ->info('If you want to prepend a string to the index name, you can set it here. This can be useful in a development setup where each developer has their own prefix. Notice that the environment is already prefixed by default, so you do not have to prefix that.')
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('credentials')
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
                ->end()
                ->arrayNode('search')
                    ->canBeEnabled()
                    ->info('Configures your site search (and autocomplete) experience')
                    ->children()
                        ->arrayNode('indexes')
                            ->info('The indexes to search (must be configured in setono_sylius_algolia.indexes). Please notice that if you enable search you MUST provide at least one index to search.')
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('routes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('product_index')
                            ->defaultValue('taxons/{slug}')
                            ->info('This is the path that should match the product index, i.e. product lists')
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('cache')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('adapter')
                            ->defaultValue('cache.adapter.filesystem')
                            ->info('The cache adapter to use')
                        ->end()
                        ->booleanNode('enabled')
                            ->defaultTrue()
                            ->info('Whether to enable cache or not')
                        ->end()
                        ->integerNode('ttl')
                            ->info('The number of seconds before an element is automatically invalidated in the cache')
                            ->defaultValue(604_800) // default is 7 days
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
