<?php

declare(strict_types=1);

namespace Setono\SyliusAlgoliaPlugin\Config;

use Setono\SyliusAlgoliaPlugin\Document\Document;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

final class IndexableResource
{
    /**
     * This is the name of the resource, i.e. sylius.product
     */
    public string $name;

    /**
     * This is the short name of the resource, i.e. the part that comes after the last . (dot) in the name.
     * For sylius.product, this would be 'product'
     */
    public string $shortName;

    /**
     * This is the FQCN for the Sylius resource, i.e. for sylius.product this could be App\Entity\Product\Product
     *
     * @var class-string<ResourceInterface&CodeAwareInterface>
     */
    public string $resourceClass;

    /**
     * This is the FQCN for the document that the above resource will be mapped to in Algolia.
     * If you are indexing products this could be Setono\SyliusAlgoliaPlugin\Document\Product
     *
     * @var class-string<Document>
     */
    public string $documentClass;

    /**
     * @param class-string<ResourceInterface&CodeAwareInterface> $resourceClass
     * @param class-string<Document> $documentClass
     */
    public function __construct(string $name, string $resourceClass, string $documentClass)
    {
        Assert::stringNotEmpty($name);
        if (!is_a($resourceClass, ResourceInterface::class, true)) {
            throw new \InvalidArgumentException(sprintf(
                'The resource class %s MUST be an instance of %s',
                $resourceClass,
                ResourceInterface::class
            ));
        }

        if (!is_a($resourceClass, CodeAwareInterface::class, true)) {
            throw new \InvalidArgumentException(sprintf(
                'The resource class %s MUST be an instance of %s',
                $resourceClass,
                CodeAwareInterface::class
            ));
        }

        if (!is_a($documentClass, Document::class, true)) {
            throw new \InvalidArgumentException(sprintf(
                'The document class %s MUST be an instance of %s',
                $documentClass,
                Document::class
            ));
        }

        $this->name = $name;
        $this->shortName = $name;

        $pos = strrpos($name, '.');
        if (is_int($pos)) {
            $this->shortName = substr($name, $pos + 1);
        }

        $this->resourceClass = $resourceClass;
        $this->documentClass = $documentClass;
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->name, $this->resourceClass);
    }
}
