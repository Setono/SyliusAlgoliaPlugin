<?php
declare(strict_types=1);

namespace Tests\Setono\SyliusAlgoliaPlugin\Application\Command;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

final class SendOrderCompleteEventCommand extends Command
{
    protected static $defaultName = 'setono:sylius-algolia:test:send-order-complete-event';

    private EventDispatcherInterface $eventDispatcher;
    private OrderRepositoryInterface $orderRepository;

    public function __construct(EventDispatcherInterface $eventDispatcher, OrderRepositoryInterface $orderRepository)
    {
        parent::__construct();

        $this->eventDispatcher = $eventDispatcher;
        $this->orderRepository = $orderRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $orders = $this->orderRepository->findBy([], [], 1);
        Assert::isArray($orders);
        Assert::notEmpty($orders);

        $order = $orders[0];
        Assert::isInstanceOf($order, OrderInterface::class);

        $event = new ResourceControllerEvent($order);
        $this->eventDispatcher->dispatch($event, 'sylius.order.post_complete');

        return 0;
    }
}
