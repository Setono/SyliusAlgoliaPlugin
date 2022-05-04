<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Client\InsightsClient;

use Setono\ClientId\ClientId;
use Sylius\Component\Channel\Model\ChannelInterface;
use Webmozart\Assert\Assert;

/**
 * Intentionally not final to make it easier for developers to extend it with their own context.
 *
 * This class represent the values (context) that were present when the event happened
 */
class EventContext
{
    /**
     * The time the event was fired
     */
    public \DateTimeImmutable $timestamp;

    public string $clientId;

    public string $channelCode;

    public string $localeCode;

    public string $currencyCode;

    public ?string $queryId;

    /**
     * @param string|ClientId $clientId
     * @param string|ChannelInterface $channel
     */
    public function __construct($clientId, $channel, string $localeCode, string $currencyCode, string $queryId = null)
    {
        if ($clientId instanceof ClientId) {
            $clientId = $clientId->toString();
        }
        Assert::string($clientId);

        if ($channel instanceof ChannelInterface) {
            $channel = $channel->getCode();
        }
        Assert::string($channel);

        $this->timestamp = new \DateTimeImmutable();
        $this->clientId = $clientId;
        $this->channelCode = $channel;
        $this->localeCode = $localeCode;
        $this->currencyCode = $currencyCode;
        $this->queryId = $queryId;
    }
}
