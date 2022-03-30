<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Webmozart\Assert\Assert;

final class RegisterResourceBasedServicesPass implements CompilerPassInterface
{
    private string $registry;

    private string $tag;

    /**
     * @param string $registry the id of the registry where the tagged services should be registered
     * @param string $tag the tag to search for
     */
    public function __construct(string $registry, string $tag)
    {
        $this->registry = $registry;
        $this->tag = $tag;
    }

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has($this->registry)) {
            return;
        }

        $registry = $container->getDefinition($this->registry);

        foreach ($container->findTaggedServiceIds($this->tag) as $id => $tags) {
            /** @var array $tag */
            foreach ($tags as $tag) {
                $priority = $tag['priority'] ?? 0;
                Assert::integer($priority);

                $registry->addMethodCall('register', [new Reference($id), $priority]);
            }
        }
    }
}
