<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Exception;

final class NonExistingIndexException extends \InvalidArgumentException
{
    /**
     * @param list<string>|null $availableIndexes
     */
    public static function fromName(string $name, array $availableIndexes = null): self
    {
        $message = sprintf('No index exists with the name "%s".', $name);

        if (null !== $availableIndexes && [] !== $availableIndexes) {
            $message .= sprintf(' Available indexes are: [%s]', implode(', ', $availableIndexes));
        }

        return new self($message);
    }
}
