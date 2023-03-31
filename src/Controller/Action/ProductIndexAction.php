<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Controller\Action;

use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResourceRegistry;
use Setono\SyliusAlgoliaPlugin\Event\ProductIndexEvent;
use Setono\SyliusAlgoliaPlugin\Resolver\IndexName\IndexNameResolverInterface;
use Setono\SyliusAlgoliaPlugin\Resolver\SortBy\SortBy;
use Setono\SyliusAlgoliaPlugin\Resolver\SortBy\SortByResolverInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class ProductIndexAction
{
    private Environment $twig;

    private IndexNameResolverInterface $indexNameResolver;

    private TaxonRepositoryInterface $taxonRepository;

    private LocaleContextInterface $localeContext;

    private EventDispatcherInterface $eventDispatcher;

    private IndexableResourceRegistry $indexableResourceRegistry;

    private SortByResolverInterface $sortByResolver;

    private TranslatorInterface $translator;

    public function __construct(
        Environment $twig,
        IndexNameResolverInterface $indexNameResolver,
        TaxonRepositoryInterface $taxonRepository,
        LocaleContextInterface $localeContext,
        EventDispatcherInterface $eventDispatcher,
        IndexableResourceRegistry $indexableResourceRegistry,
        SortByResolverInterface $sortByResolver,
        TranslatorInterface $translator
    ) {
        $this->twig = $twig;
        $this->indexNameResolver = $indexNameResolver;
        $this->taxonRepository = $taxonRepository;
        $this->localeContext = $localeContext;
        $this->eventDispatcher = $eventDispatcher;
        $this->indexableResourceRegistry = $indexableResourceRegistry;
        $this->sortByResolver = $sortByResolver;
        $this->translator = $translator;
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
            $indexableResource = $this->indexableResourceRegistry->getByClass(ProductInterface::class);
        } catch (\InvalidArgumentException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }

        $index = $this->indexNameResolver->resolve($indexableResource);

        $response = new Response($this->twig->render('@SetonoSyliusAlgoliaPlugin/shop/product/index.html.twig', [
            'index' => $index,
            'taxon' => $taxon,
            // todo translate this in the resolver?
            'sortBy' => array_map(
                function (SortBy $sortBy) {
                    $sortBy->label = $this->translator->trans($sortBy->label);

                    return $sortBy;
                },
                $this->sortByResolver->resolveFromIndexableResource($indexableResource)
            ),
        ]));

        $event = new ProductIndexEvent($response, $index, $taxon, $slug, $locale);
        $this->eventDispatcher->dispatch($event);

        return $event->response;
    }
}
