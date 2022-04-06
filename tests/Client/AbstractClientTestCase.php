<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusAlgoliaPlugin\Client;

use Algolia\AlgoliaSearch\InsightsClient as AlgoliaInsightsClient;
use Algolia\AlgoliaSearch\RecommendClient as AlgoliaRecommendClient;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\Assert;

abstract class AbstractClientTestCase extends TestCase
{
    private bool $live = false;

    protected ?AlgoliaInsightsClient $algoliaInsightsClient = null;

    protected ?AlgoliaRecommendClient $algoliaRecommendClient = null;

    protected function setUp(): void
    {
        $live = (bool) getenv('ALGOLIA_LIVE');
        if (false !== $live) {
            $this->live = true;

            $appId = getenv('ALGOLIA_APP_ID');
            Assert::stringNotEmpty($appId, 'When testing live, you need to provide the ALGOLIA_APP_ID');

            $adminApiKey = getenv('ALGOLIA_ADMIN_API_KEY');
            Assert::stringNotEmpty($adminApiKey, 'When testing live, you need to provide the ALGOLIA_ADMIN_API_KEY');

            $algoliaInsightsClient = AlgoliaInsightsClient::create($appId, $adminApiKey);
            Assert::isInstanceOf($algoliaInsightsClient, AlgoliaInsightsClient::class);
            $this->algoliaInsightsClient = $algoliaInsightsClient;

            $algoliaRecommendClient = AlgoliaRecommendClient::create($appId, $adminApiKey);
            Assert::isInstanceOf($algoliaRecommendClient, AlgoliaRecommendClient::class);
            $this->algoliaRecommendClient = $algoliaRecommendClient;
        }
    }

    /**
     * @psalm-assert-if-true !null $this->algoliaInsightsClient
     * @psalm-assert-if-true !null $this->algoliaRecommendClient
     */
    protected function isLive(): bool
    {
        return $this->live;
    }
}
