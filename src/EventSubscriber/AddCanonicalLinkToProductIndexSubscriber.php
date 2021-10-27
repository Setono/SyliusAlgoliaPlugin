<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\EventSubscriber;

use Fig\Link\GenericLinkProvider;
use Fig\Link\Link;
use Psr\Link\EvolvableLinkProviderInterface;
use Setono\SyliusAlgoliaPlugin\Event\ProductIndexEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

final class AddCanonicalLinkToProductIndexSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(RequestStack $requestStack, UrlGeneratorInterface $urlGenerator)
    {
        $this->requestStack = $requestStack;
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductIndexEvent::class => 'addLink',
        ];
    }

    public function addLink(ProductIndexEvent $event): void
    {
        $request = $this->requestStack->getMasterRequest();
        if (null === $request) {
            return;
        }

        $link = new Link('canonical', $this->urlGenerator->generate('sylius_shop_product_index', [
            'slug' => $event->slug,
        ], UrlGeneratorInterface::ABSOLUTE_URL));

        if (null === $linkProvider = $request->attributes->get('_links')) {
            $request->attributes->set('_links', new GenericLinkProvider([$link]));

            return;
        }

        Assert::isInstanceOf($linkProvider, EvolvableLinkProviderInterface::class);

        $request->attributes->set('_links', $linkProvider->withLink($link));
    }
}
