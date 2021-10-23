<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Controller\Action;

use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusAlgoliaPlugin\Event\ProductIndexEvent;
use Setono\SyliusAlgoliaPlugin\IndexResolver\ProductIndexResolverInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

final class ProductIndexAction
{
    private Environment $twig;

    private ProductIndexResolverInterface $productIndexResolver;

    private TaxonRepositoryInterface $taxonRepository;

    private LocaleContextInterface $localeContext;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        Environment $twig,
        ProductIndexResolverInterface $productIndexResolver,
        TaxonRepositoryInterface $taxonRepository,
        LocaleContextInterface $localeContext,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->twig = $twig;
        $this->productIndexResolver = $productIndexResolver;
        $this->taxonRepository = $taxonRepository;
        $this->localeContext = $localeContext;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(string $slug): Response
    {
        $locale = $this->localeContext->getLocaleCode();

        $taxon = $this->taxonRepository->findOneBySlug($slug, $locale);
        if (null === $taxon) {
            throw new NotFoundHttpException(sprintf(
                'The taxon with slug "%s" does not exist, is not enabled or is not translated in locale "%s"',
                $slug,
                $locale
            ));
        }

        $event = new ProductIndexEvent($taxon, $slug, $locale);
        $this->eventDispatcher->dispatch($event);

        return $event->response ?? new Response($this->twig->render('@SetonoSyliusAlgoliaPlugin/shop/product/index.html.twig', [
            'index' => $this->productIndexResolver->resolve(),
            'taxon' => $taxon,
        ]));
    }
}
