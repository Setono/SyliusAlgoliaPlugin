<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Command;

use Setono\SyliusAlgoliaPlugin\Message\Command\PopulateProductIndex;
use Setono\SyliusAlgoliaPlugin\Provider\ProductIndexScopesProviderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class PopulateCommand extends Command
{
    protected static $defaultName = 'setono:sylius-algolia:populate';

    /** @var string|null */
    protected static $defaultDescription = 'Populate indexes';

    private ProductIndexScopesProviderInterface $productIndexScopeProvider;

    private MessageBusInterface $commandBus;

    public function __construct(
        ProductIndexScopesProviderInterface $productIndexScopeProvider,
        MessageBusInterface $messageBus
    ) {
        parent::__construct();

        $this->productIndexScopeProvider = $productIndexScopeProvider;
        $this->commandBus = $messageBus;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $productIndexScopes = $this->productIndexScopeProvider->getProductIndexScopes();

        foreach ($productIndexScopes as $productIndexScope) {
            $this->commandBus->dispatch(new PopulateProductIndex($productIndexScope));
        }

        return 0;
    }
}
