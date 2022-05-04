<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Renderer;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Setono\SyliusAlgoliaPlugin\Provider\Recommendations\RecommendationsProviderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Twig\Environment;

final class RecommendationsRenderer implements RecommendationsRendererInterface, LoggerAwareInterface
{
    private LoggerInterface $logger;

    private RecommendationsProviderInterface $recommendationsProvider;

    private Environment $twig;

    private bool $debug;

    public function __construct(
        RecommendationsProviderInterface $recommendationsProvider,
        Environment $twig,
        bool $debug
    ) {
        $this->logger = new NullLogger();
        $this->recommendationsProvider = $recommendationsProvider;
        $this->twig = $twig;
        $this->debug = $debug;
    }

    public function renderFrequentlyBoughtTogether(ProductInterface $product, string $index, int $max = 10): string
    {
        $error = null;

        try {
            $products = [...$this->recommendationsProvider->getFrequentlyBoughtTogether($product, $index, $max)];
        } catch (\Throwable $e) {
            $products = [];
            $error = $e->getMessage();

            $this->logger->error(sprintf(
                "An error occurred while trying to fetch recommended products for product with code '%s'. The error was: %s. The trace was:\n\n%s\n",
                (string) $product->getCode(),
                $e->getMessage(),
                $e->getTraceAsString()
            ));
        }

        return $this->twig->render('@SetonoSyliusAlgoliaPlugin/shop/product/_frequently_bought_together.html.twig', [
            'product' => $product,
            'products' => $products,
            'error' => $error,
            'debug' => $this->debug,
        ]);
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
