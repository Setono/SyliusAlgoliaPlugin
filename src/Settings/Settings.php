<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Settings;

use Webmozart\Assert\Assert;

/**
 * This class holds all the shared settings between the scope 'settings' and 'search'
 *
 * todo add descriptions from docs
 *
 * See https://www.algolia.com/doc/api-reference/settings-api-parameters/
 */
class Settings
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

    final public function __construct()
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        /** @var array<string, mixed> $result */
        $result = [];

        /**
         * @var string $property
         * @var mixed $value
         */
        foreach ((array) $this as $property => $value) {
            if (null === $value || [] === $value) {
                continue;
            }

            if (is_array($value)) {
                /** @var mixed $item */
                foreach ($value as &$item) {
                    if (is_object($item) && method_exists($item, '__toString')) {
                        $item = (string) $item;
                    }
                }
                unset($item);
            }

            /** @psalm-suppress MixedAssignment */
            $result[$property] = $value;
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $settings
     *
     * @return static
     */
    public static function fromArray(array $settings): self
    {
        $obj = new static();

        /**
         * @var string $key
         * @var mixed $value
         */
        foreach ($settings as $key => $value) {
            Assert::string($key);

            if (null === $value || !property_exists(self::class, $key)) {
                continue;
            }

            $obj->{$key} = $value;
        }

        return $obj;
    }
}
