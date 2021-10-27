<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\CrawlerDetection;

use DeviceDetector\Cache\PSR6Bridge;
use DeviceDetector\Parser\Bot as BotParser;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class MatomoCrawlerDetection implements CrawlerDetectionInterface
{
    private RequestStack $requestStack;

    private CacheItemPoolInterface $cache;

    public function __construct(RequestStack $requestStack, CacheItemPoolInterface $cache)
    {
        $this->requestStack = $requestStack;
        $this->cache = $cache;
    }

    public function isCrawler(string $userAgent = null): bool
    {
        if (null === $userAgent) {
            $request = $this->requestStack->getMasterRequest();
            if (null === $request) {
                return false;
            }

            $userAgent = $request->headers->get('user-agent');
            if (null === $userAgent) {
                return false;
            }
        }

        $botParser = new BotParser();
        $botParser->setUserAgent($userAgent);
        $botParser->setCache(new PSR6Bridge($this->cache));
        $botParser->discardDetails();

        return null !== $botParser->parse();
    }
}
