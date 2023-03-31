<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Settings;

final class SortableReplica
{
    public string $name;

    public string $attribute;

    public string $order;

    public function __construct(string $name, string $attribute, string $order)
    {
        $this->name = $name;
        $this->attribute = $attribute;
        $this->order = $order;
    }

    public function ranking(): string
    {
        return sprintf('%s(%s)', $this->order, $this->attribute);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
