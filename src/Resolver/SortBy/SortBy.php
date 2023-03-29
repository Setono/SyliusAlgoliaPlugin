<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Resolver\SortBy;

final class SortBy implements \JsonSerializable
{
    public string $label;

    public string $index;

    public function __construct(string $label, string $index)
    {
        $this->label = $label;
        $this->index = $index;
    }

    /**
     * This allows you to json_encode the result of the SortByResolver and use directly in the Algolia configuration
     */
    public function jsonSerialize(): array
    {
        return [
            'label' => $this->label,
            'value' => $this->index,
        ];
    }
}
