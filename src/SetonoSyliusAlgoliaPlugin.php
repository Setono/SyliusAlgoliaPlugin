<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin;

use Setono\SyliusAlgoliaPlugin\DependencyInjection\Compiler\RegisterCompositeServicesPass;
use Setono\SyliusAlgoliaPlugin\DependencyInjection\Compiler\RegisterDataMappersPass;
use Setono\SyliusAlgoliaPlugin\DependencyInjection\Compiler\RegisterIndexableResourceCollectionPass;
use Setono\SyliusAlgoliaPlugin\DependencyInjection\Compiler\RegisterResourceBasedServicesPass;
use Setono\SyliusAlgoliaPlugin\DependencyInjection\Compiler\RegisterUrlGeneratorsPass;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SetonoSyliusAlgoliaPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterDataMappersPass());
        $container->addCompilerPass(new RegisterUrlGeneratorsPass());
        $container->addCompilerPass(new RegisterIndexableResourceCollectionPass());
        $container->addCompilerPass(new RegisterCompositeServicesPass(
            'setono_sylius_algolia.provider.index_scope.composite',
            'setono_sylius_algolia.index_scope_provider'
        ));

        // Register services in registries
        $container->addCompilerPass(new RegisterResourceBasedServicesPass(
            'setono_sylius_algolia.registry.index_settings_provider',
            'setono_sylius_algolia.index_settings_provider'
        ));

        $container->addCompilerPass(new RegisterResourceBasedServicesPass(
            'setono_sylius_algolia.registry.indexer',
            'setono_sylius_algolia.indexer'
        ));
    }
}
