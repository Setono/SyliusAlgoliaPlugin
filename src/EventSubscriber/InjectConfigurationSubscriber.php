<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\EventSubscriber;

use Setono\SyliusAlgoliaPlugin\Event\ProductIndexEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

final class InjectConfigurationSubscriber implements EventSubscriberInterface
{
    /**
     * An array of rendered HTML tags that will be inserted just before the closing </body> tag
     *
     * @var array<array-key, string>
     */
    private array $tags = [];

    private Environment $twig;

    private string $algoliaAppId;

    private string $algoliaSearchApiKey;

    public function __construct(Environment $twig, string $algoliaAppId, string $algoliaSearchApiKey)
    {
        $this->twig = $twig;
        $this->algoliaAppId = $algoliaAppId;
        $this->algoliaSearchApiKey = $algoliaSearchApiKey;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductIndexEvent::class => 'addTags',
            KernelEvents::RESPONSE => 'inject',
        ];
    }

    public function addTags(ProductIndexEvent $event): void
    {
        $this->tags[] = sprintf(
            '<div id="algolia-index" data-value="%s" style="display: none"></div>',
            $event->index
        );

        $this->tags[] = sprintf(
            '<div id="algolia-taxon" data-value="%s" style="display: none"></div>',
            (string) $event->taxon->getCode()
        );

        $this->tags[] = sprintf(
            '<script id="algolia-hit-template" type="text/template">%s</script>',
            $this->twig->render('@SetonoSyliusAlgoliaPlugin/shop/product/_item.html.twig')
        );
    }

    public function inject(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();

        // this 'if' has been copied from \Symfony\Bundle\WebProfilerBundle\EventListener\WebDebugToolbarListener::onKernelResponse()
        if ($response->isRedirection()
            || 'html' !== $request->getRequestFormat()
            || false !== stripos($response->headers->get('Content-Disposition', ''), 'attachment;')
            || ($response->headers->has('Content-Type') && strpos($response->headers->get('Content-Type', ''), 'html') === false)
        ) {
            return;
        }

        // populate defaults tags
        $this->tags[] = sprintf(
            '<div id="algolia-credentials" data-app-id="%s" data-search-api-key="%s" style="display: none"></div>',
            $this->algoliaAppId,
            $this->algoliaSearchApiKey
        );

        // this injection part has been copied from \Symfony\Bundle\WebProfilerBundle\EventListener\WebDebugToolbarListener::injectToolbar()
        $content = $response->getContent();
        if (false === $content) {
            return;
        }

        $pos = strripos($content, '</body>');

        if (false === $pos) {
            return;
        }

        $content = substr($content, 0, $pos) . "\n" . implode("\n", $this->tags) . "\n" . substr($content, $pos);
        $response->setContent($content);
    }
}
