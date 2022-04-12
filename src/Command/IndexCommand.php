<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Command;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResourceCollection;
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

    private IndexableResourceCollection $indexableResourceCollection;

    public function __construct(
        MessageBusInterface $messageBus,
        IndexableResourceCollection $indexableResourceCollection
    ) {
        parent::__construct();

        $this->commandBus = $messageBus;
        $this->indexableResourceCollection = $indexableResourceCollection;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->indexableResourceCollection as $indexableResource) {
            $this->commandBus->dispatch(new IndexResource($indexableResource));
        }

        return 0;
    }
}
