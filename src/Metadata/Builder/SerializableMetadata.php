<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Metadata\Builder;

use Hofff\JsonLd\Metadata\Metadata;
use Hofff\JsonLd\Metadata\Property;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class SerializableMetadata implements Metadata
{
    /**
     * @internal
     *
     * @var string
     */
    public $class;

    /**
     * @internal
     *
     * @var string[]|array
     */
    public $types;

    /**
     * @internal
     *
     * @var Property[]|array
     */
    public $properties;

    /**
     * {@inheritDoc}
     * @see \Hofff\JsonLd\Metadata\Metadata::getClass()
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * {@inheritDoc}
     * @see \Hofff\JsonLd\Metadata\Metadata::getTypes()
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * {@inheritDoc}
     * @see \Hofff\JsonLd\Metadata\Metadata::getProperties()
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}
