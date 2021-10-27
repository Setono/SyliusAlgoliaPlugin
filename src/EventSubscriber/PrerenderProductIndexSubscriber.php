<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\EventSubscriber;

use Setono\PrerenderBundle\Prerenderer\PrerendererInterface;
use Setono\SyliusAlgoliaPlugin\CrawlerDetection\CrawlerDetectionInterface;
use Setono\SyliusAlgoliaPlugin\Event\ProductIndexEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;

final class PrerenderProductIndexSubscriber implements EventSubscriberInterface
{
    private PrerendererInterface $prerenderer;

    private CrawlerDetectionInterface $crawlerDetection;

    public function __construct(PrerendererInterface $prerenderer, CrawlerDetectionInterface $crawlerDetection)
    {
        $this->prerenderer = $prerenderer;
        $this->crawlerDetection = $crawlerDetection;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductIndexEvent::class => 'prerender',
        ];
    }

    public function prerender(ProductIndexEvent $event): void
    {
        if (!$this->crawlerDetection->isCrawler()) {
            return;
        }

        $event->response = new Response($this->prerenderer->renderMainRequest());
    }
}
