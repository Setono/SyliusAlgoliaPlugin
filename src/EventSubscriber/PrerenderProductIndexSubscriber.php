<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\EventSubscriber;

use Setono\BotDetectionBundle\BotDetector\BotDetectorInterface;
use Setono\PrerenderBundle\Prerenderer\PrerendererInterface;
use Setono\SyliusAlgoliaPlugin\Event\ProductIndexEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;

final class PrerenderProductIndexSubscriber implements EventSubscriberInterface
{
    private PrerendererInterface $prerenderer;

    private BotDetectorInterface $botDetector;

    public function __construct(PrerendererInterface $prerenderer, BotDetectorInterface $botDetector)
    {
        $this->prerenderer = $prerenderer;
        $this->botDetector = $botDetector;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductIndexEvent::class => 'prerender',
        ];
    }

    public function prerender(ProductIndexEvent $event): void
    {
        if (!$this->botDetector->isBotRequest()) {
            return;
        }

        $event->response = new Response($this->prerenderer->renderMainRequest());
    }
}
