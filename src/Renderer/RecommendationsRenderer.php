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

    public function __construct(
        RecommendationsProviderInterface $recommendationsProvider,
        Environment $twig,
        bool $debug
    ) {
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
        }

        return $this->twig->render('@SetonoSyliusAlgoliaPlugin/shop/product/_frequently_bought_together.html.twig', [
            'product' => $product,
            'products' => $products,
            'error' => $error,
            'debug' => $this->debug,
        ]);
    }
}
