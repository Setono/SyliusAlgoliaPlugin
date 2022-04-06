<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class SendOrdersCommand extends Command
{
    protected static $defaultName = 'setono:sylius-algolia:send-orders';

    /** @var string|null */
    protected static $defaultDescription = 'Will send order data to Algolia';

    private MessageBusInterface $commandBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        parent::__construct();

        $this->commandBus = $messageBus;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return 0;
    }
}
