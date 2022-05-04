<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Event\Algolia;

use Setono\SyliusAlgoliaPlugin\Client\InsightsClient\EventContext;
use Setono\SyliusAlgoliaPlugin\Message\Event\EventInterface;

abstract class BaseEvent implements EventInterface
{
    public EventContext $eventContext;

    public function __construct(EventContext $eventContext)
    {
        $this->eventContext = $eventContext;
    }
}
