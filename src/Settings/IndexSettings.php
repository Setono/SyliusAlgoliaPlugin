<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Settings;

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
    /** @var list<string> */
    public array $searchableAttributes = [];

    /** @var list<string> */
    public array $attributesForFaceting = [];

    /** @var list<string> */
    public array $unretrievableAttributes = [];

    /** @var list<string> */
    public array $ranking = [];

    /** @var list<string> */
    public array $customRanking = [];

    /** @var list<string> */
    public array $replicas = [];

    public ?int $paginationLimitedTo = null;

    /** @var list<string> */
    public array $disableTypoToleranceOnWords = [];

    public ?string $separatorsToIndex = null;

    /** @var list<string> */
    public array $attributesToTransliterate = [];

    /** @var list<string> */
    public array $camelCaseAttributes = [];

    /**
     * See: https://www.algolia.com/doc/api-reference/api-parameters/decompoundedAttributes/
     *
     * @var list<array<string, list<string>>>
     */
    public array $decompoundedAttributes = [];

    public ?string $keepDiacriticsOnCharacters = null;

    /**
     * See: https://www.algolia.com/doc/api-reference/api-parameters/customNormalization/
     *
     * @var array<string, array<string, string>>
     */
    public array $customNormalization = [];

    /** @var list<string> */
    public array $indexLanguages = [];

    /** @var list<string> */
    public array $disablePrefixOnAttributes = [];

    /** @var list<string> */
    public array $numericAttributesForFiltering = [];

    public ?bool $allowCompressionOfIntegerArray = null;

    public ?string $attributeForDistinct = null;

    public array $userData = [];

    public array $renderingContent = [];
}
