<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RegisterUrlGeneratorsPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('setono_sylius_algolia.url_generator.composite')) {
            return;
        }

        $composite = $container->getDefinition('setono_sylius_algolia.url_generator.composite');

        foreach ($this->findAndSortTaggedServices('setono_sylius_algolia.url_generator', $container) as $service) {
            $composite->addMethodCall('add', [$service]);
        }
    }
}
