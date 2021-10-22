<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Document;

/**
 * Should not be final, so it's easier for plugin users to extend it and add more properties
 *
 * todo the properties should be nullable and then the whole object should be validated before sent to Algolia
 * todo if any validation errors occur these errors should be saved so they can be handled by the shop admins
 */
class Product implements DocumentInterface
{
    public ?int $id = null;

    public ?string $name = null;

    public ?string $image = null;

    /** @var array<array-key, string> */
    public array $taxons = [];

    public ?string $baseCurrency = null;

    public ?float $basePrice = null;

    /**
     * Example:
     *
     * [
     *     'EUR' => 98.32,
     *     'USD' => 103.92
     * ]
     *
     * @var array<string, float>
     */
    public array $prices = [
        'EUR' => 98.32,
        'USD' => 103.92,
    ];

    public function getId(): int
    {
        return (int) $this->id;
    }
}
