<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Handler\Algolia;

use Algolia\AlgoliaSearch\Exceptions\AlgoliaException;
use Algolia\AlgoliaSearch\Exceptions\BadRequestException;
use Setono\SyliusAlgoliaPlugin\Client\InsightsClient\InsightsClientInterface;
use Setono\SyliusAlgoliaPlugin\Message\Event\Algolia\ProductDetailPageViewed;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Repository\ProductRepositoryInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ProductDetailPageViewedHandler implements MessageHandlerInterface
{
    private InsightsClientInterface $insightsClient;

    private ProductRepositoryInterface $productRepository;

    public function __construct(InsightsClientInterface $insightsClient, ProductRepositoryInterface $productRepository)
    {
        $this->insightsClient = $insightsClient;
        $this->productRepository = $productRepository;
    }

    public function __invoke(ProductDetailPageViewed $message): void
    {
        $product = $this->productRepository->find($message->productId);
        if (null === $product) {
            throw new UnrecoverableMessageHandlingException(sprintf(
                'The product with id %s does not exist',
                (string) $message->productId
            ));
        }

        if (!$product instanceof ProductInterface) {
            throw new UnrecoverableMessageHandlingException(sprintf(
                'Your product resource must implement the interface, %s',
                ProductInterface::class
            ));
        }

        try {
            $this->insightsClient->sendProductDetailPageViewedEventFromProduct($product, $message->eventContext);
        } catch (AlgoliaException $e) {
            if ($e instanceof BadRequestException) {
                throw new UnrecoverableMessageHandlingException($e->getMessage(), 0, $e);
            }

            throw $e;
        }
    }
}
