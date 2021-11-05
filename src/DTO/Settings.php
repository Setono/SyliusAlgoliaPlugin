<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\DTO;

/**
 * This class holds all the shared settings between the scope 'settings' and 'search'
 *
 * todo add descriptions from docs
 * todo add type hints
 *
 * See https://www.algolia.com/doc/api-reference/settings-api-parameters/
 */
class Settings implements SettingsInterface
{
    /** @var array<array-key, string>|null */
    public ?array $attributesToRetrieve = null;

    public ?int $relevancyStrictness = null;

    public ?int $maxValuesPerFacet = null;

    public ?string $sortFacetValuesBy = null;

    /** @var array<array-key, string>|null */
    public ?array $attributesToHighlight = null;

    /** @var array<array-key, string>|null */
    public ?array $attributesToSnippet = null;

    public ?string $highlightPreTag = null;

    public ?string $highlightPostTag = null;

    public ?string $snippetEllipsisText = null;

    public ?bool $restrictHighlightAndSnippetArrays = null;

    public ?int $hitsPerPage = null;

    public ?int $minWordSizefor1Typo = null;

    public ?int $minWordSizefor2Typos = null;

    /** @psalm-var 'min'|'strict'|bool|null */
    public $typoTolerance;

    public ?bool $allowTyposOnNumericTokens = null;

    /** @var array<array-key, string>|null */
    public ?array $disableTypoToleranceOnAttributes = null;

    /** @var bool|array<array-key, string>|null */
    public $ignorePlurals;

    /** @var bool|array<array-key, string>|null */
    public $removeStopWords;

    /** @var array<array-key, string>|null */
    public ?array $queryLanguages = null;

    public ?bool $decompoundQuery = null;

    public ?bool $enableRules = null;

    public ?bool $enablePersonalization = null;

    /** @psalm-var 'prefixLast'|'prefixAll'|'prefixNone'|null */
    public ?string $queryType = null;

    /** @psalm-var 'none'|'lastWords'|'firstWords'|'allOptional'|null */
    public ?string $removeWordsIfNoResults = null;

    public ?bool $advancedSyntax = null;

    /** @var array<array-key, string>|null */
    public ?array $optionalWords = null;

    /** @var array<array-key, string>|null */
    public ?array $disableExactOnAttributes = null;

    /** @psalm-var 'attribute'|'none'|'word'|null */
    public ?string $exactOnSingleWordQuery = null;

    /** @var array<array-key, string>|null */
    public ?array $alternativesAsExact = null;

    /** @var array<array-key, string>|null */
    public ?array $advancedSyntaxFeatures = null;

    /** @psalm-var 0|1|2|3|null */
    public ?int $distinct = null;

    public ?bool $replaceSynonymsInHighlight = null;

    public ?int $minProximity = null;

    /** @var array<array-key, string>|null */
    public ?array $responseFields = null;

    public ?int $maxFacetHits = null;

    public ?bool $attributeCriteriaComputedByMinProximity = null;

    public function toArray(): array
    {
        return array_filter((array) $this);
    }
}
