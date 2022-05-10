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
    /** @var list<string>|null */
    public ?array $searchableAttributes = null;

    /** @var list<string>|null */
    public ?array $attributesForFaceting = null;

    /** @var list<string>|null */
    public ?array $unretrievableAttributes = null;

    /** @var list<string>|null */
    public ?array $ranking = null;

    /** @var list<string>|null */
    public ?array $customRanking = null;

    /** @var list<string>|null */
    public ?array $replicas = null;

    public ?int $paginationLimitedTo = null;

    /** @var list<string>|null */
    public ?array $disableTypoToleranceOnWords = null;

    public ?string $separatorsToIndex = null;

    /** @var list<string>|null */
    public ?array $attributesToTransliterate = null;

    /** @var list<string>|null */
    public ?array $camelCaseAttributes = null;

    /** @var array<string, list<string>>|null */
    public ?array $decompoundedAttributes = null;

    public ?string $keepDiacriticsOnCharacters = null;

    /** @var array<string, array<string, string>>|null */
    public ?array $customNormalization = null;

    /** @var list<string>|null */
    public ?array $indexLanguages = null;

    /** @var list<string>|null */
    public ?array $disablePrefixOnAttributes = null;

    /** @var list<string>|null */
    public ?array $numericAttributesForFiltering = null;

    public ?bool $allowCompressionOfIntegerArray = null;

    public ?string $attributeForDistinct = null;

    public ?array $userData = null;

    public ?array $renderingContent = null;
}
