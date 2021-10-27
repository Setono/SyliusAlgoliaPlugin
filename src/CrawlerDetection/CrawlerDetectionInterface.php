<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\CrawlerDetection;

interface CrawlerDetectionInterface
{
    /**
     * If the $userAgent is null it will extract the user agent from the main request
     */
    public function isCrawler(string $userAgent = null): bool;
}
