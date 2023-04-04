<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Exception;

final class NonExistingResourceException extends \InvalidArgumentException
{
    /**
     * @param list<string>|null $availableResources
     */
    public static function fromNameAndIndex(string $name, string $index, array $availableResources = null): self
    {
        $message = sprintf('No resource exists with the name "%s" on the index "%s".', $name, $index);

        if (null !== $availableResources && [] !== $availableResources) {
            $message .= sprintf(' Available resources are: [%s]', implode(', ', $availableResources));
        }

        return new self($message);
    }
}
