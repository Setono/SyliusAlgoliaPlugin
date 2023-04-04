<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Command;

use Setono\SyliusAlgoliaPlugin\Config\IndexRegistry;
use Setono\SyliusAlgoliaPlugin\Message\Command\Index;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class IndexCommand extends Command
{
    protected static $defaultName = 'setono:sylius-algolia:index';

    /** @var string|null */
    protected static $defaultDescription = 'Will index all configured indexes';

    private MessageBusInterface $commandBus;

    private IndexRegistry $indexRegistry;

    public function __construct(
        MessageBusInterface $messageBus,
        IndexRegistry $indexRegistry
    ) {
        parent::__construct();

        $this->commandBus = $messageBus;
        $this->indexRegistry = $indexRegistry;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->indexRegistry as $index) {
            $this->commandBus->dispatch(new Index($index));
        }

        return 0;
    }
}
