<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Event\Algolia;

use Setono\SyliusAlgoliaPlugin\Client\InsightsClient\EventContext;
use Sylius\Component\Product\Model\ProductInterface;

final class ProductDetailPageViewed extends BaseEvent
{
    /**
     * This is the product id
     *
     * @var mixed
     */
    public $product;

    /**
     * @param mixed|ProductInterface $product
     */
    public function __construct(EventContext $eventContext, $product)
    {
        parent::__construct($eventContext);

        if ($product instanceof ProductInterface) {
            /** @var mixed $product */
            $product = $product->getId();
        }

        $this->product = $product;
    }
}
