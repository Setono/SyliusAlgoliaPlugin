<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\EventContext;

use Setono\SyliusAlgoliaPlugin\Client\InsightsClient\EventContext;

interface EventContextProviderInterface
{
    public function getEventContext(): EventContext;
}
