<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Message\Command;

use Setono\SyliusAlgoliaPlugin\Config\Index as IndexConfig;

final class Index implements CommandInterface
{
    /**
     * This is the index to be indexed
     */
    public string $index;

    /**
     * @param string|IndexConfig $index
     */
    public function __construct($index)
    {
        if ($index instanceof IndexConfig) {
            $index = $index->name;
        }

        $this->index = $index;
    }
}
