<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RegisterCompositeServicesPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    private string $compositeServiceId;

    private string $tag;

    public function __construct(string $compositeServiceId, string $tag)
    {
        $this->compositeServiceId = $compositeServiceId;
        $this->tag = $tag;
    }

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition($this->compositeServiceId)) {
            return;
        }

        $composite = $container->getDefinition($this->compositeServiceId);

        foreach ($this->findAndSortTaggedServices($this->tag, $container) as $service) {
            $composite->addMethodCall('add', [$service]);
        }
    }
}
