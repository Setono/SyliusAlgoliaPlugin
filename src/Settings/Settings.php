<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Settings;

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
    /** @var list<string> */
    public array $attributesToRetrieve = [];

    public ?int $relevancyStrictness = null;

    public ?int $maxValuesPerFacet = null;

    public ?string $sortFacetValuesBy = null;

    /** @var list<string> */
    public array $attributesToHighlight = [];

    /** @var list<string> */
    public array $attributesToSnippet = [];

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

    /** @var list<string> */
    public array $disableTypoToleranceOnAttributes = [];

    /** @var bool|list<string>|null */
    public $ignorePlurals;

    /** @var bool|list<string>|null */
    public $removeStopWords;

    /** @var list<string> */
    public array $queryLanguages = [];

    public ?bool $decompoundQuery = null;

    public ?bool $enableRules = null;

    public ?bool $enablePersonalization = null;

    /** @psalm-var 'prefixLast'|'prefixAll'|'prefixNone'|null */
    public ?string $queryType = null;

    /** @psalm-var 'none'|'lastWords'|'firstWords'|'allOptional'|null */
    public ?string $removeWordsIfNoResults = null;

    public ?bool $advancedSyntax = null;

    /** @var list<string> */
    public array $optionalWords = [];

    /** @var list<string> */
    public array $disableExactOnAttributes = [];

    /** @psalm-var 'attribute'|'none'|'word'|null */
    public ?string $exactOnSingleWordQuery = null;

    /** @var list<string> */
    public array $alternativesAsExact = [];

    /** @var list<string> */
    public array $advancedSyntaxFeatures = [];

    /** @psalm-var 0|1|2|3|null */
    public ?int $distinct = null;

    public ?bool $replaceSynonymsInHighlight = null;

    public ?int $minProximity = null;

    /** @var list<string> */
    public array $responseFields = [];

    public ?int $maxFacetHits = null;

    public ?bool $attributeCriteriaComputedByMinProximity = null;

    public function toArray(): array
    {
        return array_filter((array) $this, static function ($val): bool {
            if (null === $val) {
                return false;
            }

            if ([] === $val) {
                return false;
            }

            return true;
        });
    }
}
