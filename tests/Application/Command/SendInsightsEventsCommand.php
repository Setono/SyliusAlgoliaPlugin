<?php
declare(strict_types=1);

namespace Tests\Setono\SyliusAlgoliaPlugin\Application\Command;

use Setono\SyliusAlgoliaPlugin\Client\InsightsClient\Event;
use Setono\SyliusAlgoliaPlugin\Client\InsightsClient\InsightsClientInterface;
use Setono\SyliusAlgoliaPlugin\Model\ObjectIdAwareInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Uid\Uuid;

final class SendInsightsEventsCommand extends Command
{
    protected static $defaultName = 'setono:sylius-algolia:test:send-insights-events';
    private ProductRepositoryInterface $productRepository;
    private InsightsClientInterface $insightsClient;

    public function __construct(ProductRepositoryInterface $productRepository, InsightsClientInterface $insightsClient)
    {
        parent::__construct();

        $this->productRepository = $productRepository;
        $this->insightsClient = $insightsClient;
    }

    protected function configure(): void
    {
        $this->addOption('max', null, InputOption::VALUE_REQUIRED, 'The number of events to generate', 5000);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $max = (int) $input->getOption('max');

        /** @var list<ObjectIdAwareInterface> $products */
        $products = $this->productRepository->findAll();
        $productCount = count($products);
        $io->writeln(sprintf('Number of products: %d', $productCount));

        $io->progressStart($max);
        $events = [];

        $now = new \DateTimeImmutable();

        for($i = 1; $i <= $max; $i++) {
            $objectIds = [];
            $productsToInclude = random_int(2, 10);
            for($n = 0; $n < $productsToInclude; $n++) {
                $objectIds[] = $products[random_int(0, $productCount - 1)]->getObjectId();
            }

            $event = new Event(Event::EVENT_TYPE_CONVERSION, Event::EVENT_NAME, 'products__fashion_web__en_us__usd__dev', (string) Uuid::v4(), $objectIds);
            $event->timestamp = (int) $now->sub(new \DateInterval(sprintf('P%dD', random_int(0, 3))))->format('Uv');
            $events[] = $event;

            if($i % 20 === 0) {
                $this->insightsClient->sendEvents($events);
                $events = [];
                $io->progressAdvance(20);
            }
        }

        if(count($events) > 0) {
            $this->insightsClient->sendEvents($events);
        }

        $io->progressFinish();

        return 0;
    }
}
