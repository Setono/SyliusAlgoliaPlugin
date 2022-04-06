<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Client\InsightsClient;

/**
 * This class represents a certain context an event has happened in
 * Read about it here: https://www.algolia.com/doc/guides/sending-events/implementing/how-to/sending-events-backend/
 */
final class EventContext
{
    public string $index;

    public ?string $queryId;

    public function __construct(string $indexName, string $queryId = null)
    {
        $this->index = $indexName;
        $this->queryId = $queryId;
    }
}
