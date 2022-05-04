<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Event\Algolia;

use Setono\SyliusAlgoliaPlugin\Client\InsightsClient\EventContext;
use Sylius\Component\Product\Model\ProductInterface;

final class ProductDetailPageViewed extends BaseEvent
{
    /** @var mixed */
    public $productId;

    public function __construct(EventContext $eventContext, ProductInterface $product)
    {
        parent::__construct($eventContext);

        $this->productId = $product->getId();
    }
}
