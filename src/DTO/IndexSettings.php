<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DTO;

/**
 * This class holds all the settings that are unique to the scope 'settings'
 *
 * todo add descriptions from docs
 * todo add type hints
 *
 * See https://www.algolia.com/doc/api-reference/settings-api-parameters/
 */
class IndexSettings extends Settings
{
    public $searchableAttributes;

    /** @var array<array-key, string>|null */
    public ?array $attributesForFaceting = null;

    public $unretrievableAttributes;

    public $ranking;

    public $customRanking;

    public $replicas;

    public $paginationLimitedTo;

    public $disableTypoToleranceOnWords;

    public $separatorsToIndex;

    public $attributesToTransliterate;

    public $camelCaseAttributes;

    public $decompoundedAttributes;

    public $keepDiacriticsOnCharacters;

    public $customNormalization;

    public $indexLanguages;

    public $disablePrefixOnAttributes;

    public $numericAttributesForFiltering;

    public $allowCompressionOfIntegerArray;

    public $attributeForDistinct;

    public $userData;

    public $renderingContent;
}
