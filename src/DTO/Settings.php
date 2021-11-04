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
class Settings
{
    public $attributesToRetrieve;

    public $relevancyStrictness;

    public $maxValuesPerFacet;

    public $sortFacetValuesBy;

    public $attributesToHighlight;

    public $attributesToSnippet;

    public $highlightPreTag;

    public $highlightPostTag;

    public $snippetEllipsisText;

    public $restrictHighlightAndSnippetArrays;

    public $hitsPerPage;

    public $minWordSizefor1Typo;

    public $minWordSizefor2Typos;

    public $typoTolerance;

    public $allowTyposOnNumericTokens;

    public $disableTypoToleranceOnAttributes;

    public $ignorePlurals;

    public $removeStopWords;

    public $queryLanguages;

    public $decompoundQuery;

    public $enableRules;

    public $enablePersonalization;

    public $queryType;

    public $removeWordsIfNoResults;

    public $advancedSyntax;

    public $optionalWords;

    public $disableExactOnAttributes;

    public $exactOnSingleWordQuery;

    public $alternativesAsExact;

    public $advancedSyntaxFeatures;

    public $distinct;

    public $replaceSynonymsInHighlight;

    public $minProximity;

    public $responseFields;

    public $maxFacetHits;

    public $attributeCriteriaComputedByMinProximity;

    public function toArray(): array
    {
        return array_filter((array) $this);
    }
}
