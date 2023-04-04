<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DependencyInjection\Compiler;

use Setono\SyliusAlgoliaPlugin\Config\Index;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\Document\Document;
use Setono\SyliusAlgoliaPlugin\Exception\InvalidSyliusResourceException;
use Setono\SyliusAlgoliaPlugin\Exception\NonExistingIndexerException;
use Setono\SyliusAlgoliaPlugin\Model\IndexableInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterIndexesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('setono_sylius_algolia.config.index_registry')
            || !$container->hasParameter('setono_sylius_algolia.indexes')
            || !$container->hasParameter('sylius.resources')
        ) {
            return;
        }

        $indexers = array_keys($container->findTaggedServiceIds('setono_sylius_algolia.indexer'));
        $indexRegistry = $container->getDefinition('setono_sylius_algolia.config.index_registry');

        /** @var array<string, array{document: class-string<Document>, indexer: string, resources: list<string>}> $indexes */
        $indexes = $container->getParameter('setono_sylius_algolia.indexes');

        /** @var array<string, array{classes: array{model: class-string<IndexableInterface>}}> $syliusResources */
        $syliusResources = $container->getParameter('sylius.resources');

        foreach ($indexes as $indexName => $index) {
            $configuredResources = [];

            foreach ($index['resources'] as $resource) {
                if (!isset($syliusResources[$resource])) {
                    throw InvalidSyliusResourceException::fromName($resource, array_keys($syliusResources));
                }

                $resourceDefinitionName = sprintf('setono_sylius_algolia.index.%s.resource.%s', $indexName, $resource);
                $container->setDefinition($resourceDefinitionName, new Definition(IndexableResource::class, [
                    $resource, $syliusResources[$resource]['classes']['model'],
                ]));

                $configuredResources[$resource] = new Reference($resourceDefinitionName);
            }

            if (!in_array($index['indexer'], $indexers, true)) {
                throw NonExistingIndexerException::fromServiceId($index['indexer'], $indexers);
            }

            $indexDefinitionName = sprintf('setono_sylius_algolia.index.%s', $indexName);
            $container->setDefinition($indexDefinitionName, new Definition(Index::class, [
                $indexName,
                $index['document'],
                new Reference($index['indexer']),
                $configuredResources,
            ]));

            $indexRegistry->addMethodCall('add', [new Reference($indexDefinitionName)]);
        }
    }
}
