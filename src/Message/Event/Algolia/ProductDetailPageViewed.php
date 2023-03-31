<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Event\Algolia;

use Setono\SyliusAlgoliaPlugin\Client\InsightsClient\EventContext;
use Sylius\Component\Product\Model\ProductInterface;

final class ProductDetailPageViewed extends BaseEvent
{
    /** @var int|string */
    public $product;

    /**
     * @param int|string|ProductInterface $product
     */
    public function __construct(EventContext $eventContext, $product)
    {
        parent::__construct($eventContext);

        if ($product instanceof ProductInterface) {
            /** @var mixed $product */
            $product = $product->getId();
        }

        if (!is_string($product) && !is_int($product)) {
            throw new \InvalidArgumentException(sprintf('The product must be an instance of int|string|%s', ProductInterface::class));
        }

        $this->product = $product;
    }
}
