<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DependencyInjection\Compiler;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResourceCollection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Webmozart\Assert\Assert;

final class RegisterIndexableResourceCollectionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('sylius.resources') || !$container->hasParameter('setono_sylius_algolia.indexable_resources')) {
            return;
        }

        /** @var array<string, array{classes: array<string, class-string>}> $resources */
        $resources = $container->getParameter('sylius.resources');

        /** @var array<string, array> $indexableResources */
        $indexableResources = $container->getParameter('setono_sylius_algolia.indexable_resources');

        $definition = new Definition(IndexableResourceCollection::class);

        foreach (array_keys($indexableResources) as $indexableResourceName) {
            Assert::keyExists($resources, $indexableResourceName, sprintf('The resource "%s" is not a valid Sylius resource', $indexableResourceName));

            $indexableResourceDefinitionId = sprintf('setono_sylius_algolia.indexable_resource.%s', $indexableResourceName);
            $container->setDefinition(
                $indexableResourceDefinitionId,
                new Definition(IndexableResource::class, [$indexableResourceName, $resources[$indexableResourceName]['classes']['model']])
            );

            $definition->addArgument(new Reference($indexableResourceDefinitionId));
        }

        $container->setDefinition('setono_sylius_algolia.config.indexable_resource_collection', $definition);
        $container->getParameterBag()->remove('setono_sylius_algolia.indexable_resources');
    }
}
