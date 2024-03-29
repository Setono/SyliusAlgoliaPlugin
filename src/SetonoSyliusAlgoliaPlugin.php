<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin;

use Setono\SyliusAlgoliaPlugin\DependencyInjection\Compiler\RegisterCompositeServicesPass;
use Setono\SyliusAlgoliaPlugin\DependencyInjection\Compiler\RegisterIndexesPass;
use Setono\SyliusAlgoliaPlugin\DependencyInjection\Compiler\RegisterTwigVariablePass;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SetonoSyliusAlgoliaPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterTwigVariablePass());
        $container->addCompilerPass(new RegisterIndexesPass());

        // Register services in composite services
        $container->addCompilerPass(new RegisterCompositeServicesPass(
            'setono_sylius_algolia.data_mapper.composite',
            'setono_sylius_algolia.data_mapper'
        ));

        $container->addCompilerPass(new RegisterCompositeServicesPass(
            'setono_sylius_algolia.url_generator.composite',
            'setono_sylius_algolia.url_generator'
        ));

        $container->addCompilerPass(new RegisterCompositeServicesPass(
            'setono_sylius_algolia.provider.index_scope.composite',
            'setono_sylius_algolia.index_scope_provider'
        ));

        $container->addCompilerPass(new RegisterCompositeServicesPass(
            'setono_sylius_algolia.filter.doctrine.composite',
            'setono_sylius_algolia.doctrine_filter'
        ));

        $container->addCompilerPass(new RegisterCompositeServicesPass(
            'setono_sylius_algolia.filter.object.composite',
            'setono_sylius_algolia.object_filter'
        ));
    }
}
