<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Controller\Action;

use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusAlgoliaPlugin\Config\IndexableResourceCollection;
use Setono\SyliusAlgoliaPlugin\Event\ProductIndexEvent;
use Setono\SyliusAlgoliaPlugin\IndexNameResolver\IndexNameResolverInterface;
use Setono\SyliusAlgoliaPlugin\Registry\ResourceBasedRegistryInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

final class ProductIndexAction
{
    private Environment $twig;

    /** @var ResourceBasedRegistryInterface<IndexNameResolverInterface> */
    private ResourceBasedRegistryInterface $indexNameResolverRegistry;

    private TaxonRepositoryInterface $taxonRepository;

    private LocaleContextInterface $localeContext;

    private EventDispatcherInterface $eventDispatcher;

    private IndexableResourceCollection $indexableResourceCollection;

    private string $algoliaAppId;

    private string $algoliaSearchApiKey;

    public function __construct(
        Environment $twig,
        ResourceBasedRegistryInterface $indexNameResolverRegistry,
        TaxonRepositoryInterface $taxonRepository,
        LocaleContextInterface $localeContext,
        EventDispatcherInterface $eventDispatcher,
        IndexableResourceCollection $indexableResourceCollection,
        string $algoliaAppId,
        string $algoliaSearchApiKey
    ) {
        $this->twig = $twig;
        $this->indexNameResolverRegistry = $indexNameResolverRegistry;
        $this->taxonRepository = $taxonRepository;
        $this->localeContext = $localeContext;
        $this->eventDispatcher = $eventDispatcher;
        $this->indexableResourceCollection = $indexableResourceCollection;
        $this->algoliaAppId = $algoliaAppId;
        $this->algoliaSearchApiKey = $algoliaSearchApiKey;
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

        /** @var IndexNameResolverInterface $indexNameResolver */
        $indexNameResolver = $this->indexNameResolverRegistry->get($indexableResource);

        $index = $indexNameResolver->resolve($indexableResource);

        $response = new Response($this->twig->render('@SetonoSyliusAlgoliaPlugin/shop/product/index.html.twig', [
            'index' => $index,
            'taxon' => $taxon,
            'algolia_app_id' => $this->algoliaAppId,
            'algolia_search_api_key' => $this->algoliaSearchApiKey,
        ]));

        $event = new ProductIndexEvent($response, $index, $taxon, $slug, $locale);
        $this->eventDispatcher->dispatch($event);

        return $event->response;
    }
}
