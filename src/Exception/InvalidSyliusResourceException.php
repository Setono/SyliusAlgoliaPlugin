<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Exception;

final class InvalidSyliusResourceException extends \InvalidArgumentException
{
    /**
     * @param list<string>|null $availableSyliusResources
     */
    public static function fromName(string $name, array $availableSyliusResources = null): self
    {
        $message = sprintf('No Sylius resource exists with the name "%s".', $name);

        if (null !== $availableSyliusResources && [] !== $availableSyliusResources) {
            $message .= sprintf(' Available Sylius resources are: [%s]', implode(', ', $availableSyliusResources));
        }

        return new self($message);
    }
}
