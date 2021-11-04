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
    /** @var array<array-key, string>|null */
    public ?array $searchableAttributes = null;

    /** @var array<array-key, string>|null */
    public ?array $attributesForFaceting = null;

    /** @var array<array-key, string>|null */
    public ?array $unretrievableAttributes = null;

    /** @var array<array-key, string>|null */
    public ?array $ranking = null;

    /** @var array<array-key, string>|null */
    public ?array $customRanking = null;

    /** @var array<array-key, string>|null */
    public ?array $replicas = null;

    public ?int $paginationLimitedTo = null;

    /** @var array<array-key, string>|null */
    public ?array $disableTypoToleranceOnWords = null;

    public ?string $separatorsToIndex = null;

    /** @var array<array-key, string>|null */
    public ?array $attributesToTransliterate = null;

    /** @var array<array-key, string>|null */
    public ?array $camelCaseAttributes = null;

    /** @var array<string, array<array-key, string>>|null */
    public ?array $decompoundedAttributes = null;

    public ?string $keepDiacriticsOnCharacters = null;

    /** @var array<string, array<string, string>>|null */
    public ?array $customNormalization = null;

    /** @var array<array-key, string>|null */
    public ?array $indexLanguages = null;

    /** @var array<array-key, string>|null */
    public ?array $disablePrefixOnAttributes = null;

    /** @var array<array-key, string>|null */
    public ?array $numericAttributesForFiltering = null;

    public ?bool $allowCompressionOfIntegerArray = null;

    public ?string $attributeForDistinct = null;

    public ?array $userData = null;

    public ?array $renderingContent = null;
}
