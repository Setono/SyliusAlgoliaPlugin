<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin;

use Setono\SyliusAlgoliaPlugin\DependencyInjection\Compiler\RegisterDataMappersPass;
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
    }
}
