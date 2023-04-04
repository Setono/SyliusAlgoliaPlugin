<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Exception;

final class NonExistingIndexerException extends \InvalidArgumentException
{
    /**
     * @param list<string>|null $availableIndexers
     */
    public static function fromServiceId(string $id, array $availableIndexers = null): self
    {
        $message = sprintf('No indexer exists with the service id "%s".', $id);

        if (null !== $availableIndexers && [] !== $availableIndexers) {
            $message .= sprintf(' Available indexers are: [%s]', implode(', ', $availableIndexers));
        }

        return new self($message);
    }
}
