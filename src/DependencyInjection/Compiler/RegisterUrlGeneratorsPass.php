<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterUrlGeneratorsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('setono_sylius_algolia.url_generator.composite_resource')) {
            return;
        }

        $composite = $container->getDefinition('setono_sylius_algolia.url_generator.composite_resource');

        foreach (array_keys($container->findTaggedServiceIds('setono_sylius_algolia.url_generator')) as $id) {
            $composite->addMethodCall('add', [new Reference($id)]);
        }
    }
}
