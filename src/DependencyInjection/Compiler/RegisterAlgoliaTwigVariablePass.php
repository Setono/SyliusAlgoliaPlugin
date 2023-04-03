<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterAlgoliaTwigVariablePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('twig') || !$container->hasDefinition('setono_sylius_algolia.twig.algolia_variable')) {
            return;
        }

        $container->getDefinition('twig')
            ->addMethodCall('addGlobal', ['algolia', new Reference('setono_sylius_algolia.twig.algolia_variable')])
        ;
    }
}
