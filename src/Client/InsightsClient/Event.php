<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Client\InsightsClient;

use Webmozart\Assert\Assert;

/**
 * todo add all event parameters, see https://www.algolia.com/doc/api-reference/api-methods/send-events/
 */
final class Event
{
    public const EVENT_TYPE_CLICK = 'click';

    public const EVENT_TYPE_CONVERSION = 'conversion';

    public const EVENT_TYPE_VIEW = 'view';

    /**
     * You can use anything for event names as long as you make sure to use the same name when you send the same event.
     * These constants are just provided to make that easier
     */
    public const EVENT_NAME_PRODUCT_PURCHASED = 'Product Purchased';

    public const EVENT_NAME_PRODUCT_DETAIL_PAGE_VIEWED = 'Product Detail Page Viewed';

    public string $eventType;

    public string $eventName;

    public string $index;

    public string $userToken;

    /** @var list<string> */
    public array $objectIds;

    /**
     * The time of the event in milliseconds
     */
    public int $timestamp;

    public ?string $queryId;

    /**
     * @param list<string> $objectIds
     * @param int|\DateTimeInterface|null $timestamp
     */
    public function __construct(string $eventType, string $eventName, string $index, string $userToken, array $objectIds, $timestamp = null, string $queryId = null)
    {
        Assert::oneOf($eventType, self::getEventTypes());

        $this->eventType = $eventType;
        $this->eventName = $eventName;
        $this->index = $index;
        $this->userToken = $userToken;
        $this->objectIds = $objectIds;

        if ($timestamp instanceof \DateTimeInterface) {
            $timestamp = (int) $timestamp->format('Uv');
        }
        $timestamp = $timestamp ?? time() * 1000;
        Assert::integer($timestamp);

        $this->timestamp = $timestamp;
        $this->queryId = $queryId;
    }

    /**
     * @return list<string>
     */
    public static function getEventTypes(): array
    {
        return [
            self::EVENT_TYPE_CLICK,
            self::EVENT_TYPE_CONVERSION,
            self::EVENT_TYPE_VIEW,
        ];
    }
}
