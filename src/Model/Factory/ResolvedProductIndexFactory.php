<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Model\Factory;

use Setono\SyliusAlgoliaPlugin\Model\ResolvedProductIndexInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

final class ResolvedProductIndexFactory implements ResolvedProductIndexFactoryInterface
{
    private string $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function createNew(
        ?ChannelInterface $channel = null,
        ?LocaleInterface $locale = null,
        ?CurrencyInterface $currency = null
    ): ResolvedProductIndexInterface {
        return new $this->className($channel, $locale, $currency);
    }
}
