<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

final class IndexProducts
{
    private string $indexName;

    private string $productsDql;

    private Collection $productsQueryParameters;

    public ?ChannelInterface $channel;

    public ?LocaleInterface $locale;

    public ?CurrencyInterface $currency;

    public function __construct(
        string $indexName,
        string $productsDql,
        Collection $productsQueryParameters,
        ?ChannelInterface $channel = null,
        ?LocaleInterface $locale = null,
        ?CurrencyInterface $currency = null
    ) {
        $this->indexName = $indexName;
        $this->productsDql = $productsDql;
        $this->productsQueryParameters = $productsQueryParameters;
        $this->channel = $channel;
        $this->locale = $locale;
        $this->currency = $currency;
    }

    public function getIndexName(): string
    {
        return $this->indexName;
    }

    public function getProductsDql(): string
    {
        return $this->productsDql;
    }

    public function getProductsQueryParameters(): Collection
    {
        return $this->productsQueryParameters;
    }

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function getLocale(): ?LocaleInterface
    {
        return $this->locale;
    }

    public function getCurrency(): ?CurrencyInterface
    {
        return $this->currency;
    }
}
