<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Command;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResourceRegistry;
use Setono\SyliusAlgoliaPlugin\Message\Command\IndexResource;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class IndexCommand extends Command
{
    protected static $defaultName = 'setono:sylius-algolia:index';

    /** @var string|null */
    protected static $defaultDescription = 'Will index all (indexable) resources';

    private MessageBusInterface $commandBus;

    private IndexableResourceRegistry $indexableResourceRegistry;

    public function __construct(
        MessageBusInterface $messageBus,
        IndexableResourceRegistry $indexableResourceRegistry
    ) {
        parent::__construct();

        $this->commandBus = $messageBus;
        $this->indexableResourceRegistry = $indexableResourceRegistry;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->indexableResourceRegistry as $indexableResource) {
            $this->commandBus->dispatch(new IndexResource($indexableResource));
        }

        return 0;
    }
}
