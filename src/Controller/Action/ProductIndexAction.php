<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Controller\Action;

use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResourceCollection;
use Setono\SyliusAlgoliaPlugin\Event\ProductIndexEvent;
use Setono\SyliusAlgoliaPlugin\IndexNameResolver\IndexNameResolverInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

final class ProductIndexAction
{
    private Environment $twig;

    private IndexNameResolverInterface $indexNameResolver;

    private TaxonRepositoryInterface $taxonRepository;

    private LocaleContextInterface $localeContext;

    private EventDispatcherInterface $eventDispatcher;

    private IndexableResourceCollection $indexableResourceCollection;

    public function __construct(
        Environment $twig,
        IndexNameResolverInterface $indexNameResolver,
        TaxonRepositoryInterface $taxonRepository,
        LocaleContextInterface $localeContext,
        EventDispatcherInterface $eventDispatcher,
        IndexableResourceCollection $indexableResourceCollection
    ) {
        $this->twig = $twig;
        $this->indexNameResolver = $indexNameResolver;
        $this->taxonRepository = $taxonRepository;
        $this->localeContext = $localeContext;
        $this->eventDispatcher = $eventDispatcher;
        $this->indexableResourceCollection = $indexableResourceCollection;
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

        try {
            $indexableResource = $this->indexableResourceCollection->getByClass(ProductInterface::class);
        } catch (\InvalidArgumentException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }

        $index = $this->indexNameResolver->resolve($indexableResource);

        $response = new Response($this->twig->render('@SetonoSyliusAlgoliaPlugin/shop/product/index.html.twig', [
            'index' => $index,
            'taxon' => $taxon,
        ]));

        $event = new ProductIndexEvent($response, $index, $taxon, $slug, $locale);
        $this->eventDispatcher->dispatch($event);

        return $event->response;
    }
}
