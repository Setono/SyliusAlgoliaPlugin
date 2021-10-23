<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Controller\Action;

use Setono\SyliusAlgoliaPlugin\IndexResolver\ProductIndexResolverInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class ProductIndexAction
{
    private Environment $twig;

    private ProductIndexResolverInterface $productIndexResolver;

    public function __construct(Environment $twig, ProductIndexResolverInterface $productIndexResolver)
    {
        $this->twig = $twig;
        $this->productIndexResolver = $productIndexResolver;
    }

    public function __invoke(): Response
    {
        return new Response($this->twig->render('@SetonoSyliusAlgoliaPlugin/shop/product/index.html.twig', [
            'index' => $this->productIndexResolver->resolve(),
        ]));
    }
}
