<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Renderer;

use Setono\SyliusAlgoliaPlugin\Provider\Recommendations\RecommendationsProviderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Twig\Environment;

final class RecommendationsRenderer implements RecommendationsRendererInterface
{
    private RecommendationsProviderInterface $recommendationsProvider;

    private Environment $twig;

    private bool $debug;

    public function __construct(RecommendationsProviderInterface $recommendationsProvider, Environment $twig, bool $debug)
    {
        $this->recommendationsProvider = $recommendationsProvider;
        $this->twig = $twig;
        $this->debug = $debug;
    }

    public function renderFrequentlyBoughtTogether(ProductInterface $product, string $index, int $max = 10): string
    {
        try {
            return $this->twig->render('@SetonoSyliusAlgoliaPlugin/shop/product/_frequently_bought_together.html.twig', [
                'product' => $product,
                'products' => [...$this->recommendationsProvider->getFrequentlyBoughtTogether($product, $index, $max)],
            ]);
        } catch (\Throwable $e) {
            return $this->twig->render('@SetonoSyliusAlgoliaPlugin/shop/product/frequently_bought_together/error.html.twig', [
                'product' => $product,
                'error' => $e->getMessage(),
                'debug' => $this->debug,
            ]);
        }
    }
}
