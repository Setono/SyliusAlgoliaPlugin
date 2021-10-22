<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Controller\Action;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class ProductIndexAction
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke(): Response
    {
        return new Response($this->twig->render('@SetonoSyliusAlgoliaPlugin/shop/product/index.html.twig'));
    }
}
