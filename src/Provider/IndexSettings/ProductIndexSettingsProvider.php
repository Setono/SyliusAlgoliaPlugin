<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Provider\IndexSettings;

use Setono\SyliusAlgoliaPlugin\Config\IndexableResource;
use Setono\SyliusAlgoliaPlugin\IndexScope\IndexScope;
use Setono\SyliusAlgoliaPlugin\Settings\IndexSettings;
use Sylius\Component\Core\Model\ProductInterface;

final class ProductIndexSettingsProvider extends AbstractIndexSettingsProvider
{
    public function getSettings(IndexScope $indexScope): IndexSettings
    {
        $settings = parent::getSettings($indexScope);

        $settings->searchableAttributes = ['code', 'name'];
        $settings->attributesForFaceting = [
            'filterOnly(taxonCodes)', 'onSale', 'price',
            'taxons.lvl0', 'taxons.lvl1', 'taxons.lvl2', // todo these should somehow be dynamically generated
        ];
        $settings->customRanking = ['desc(createdAt)'];
        $settings->disablePrefixOnAttributes = ['code'];
        $settings->ignorePlurals = true; // remember to set query languages
        $settings->allowTyposOnNumericTokens = false;

        return $settings;
    }

    public function supports($resource): bool
    {
        $class = $resource instanceof IndexableResource ? $resource->className : get_class($resource);

        return is_a($class, ProductInterface::class, true);
    }
}
