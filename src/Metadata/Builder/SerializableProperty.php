<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Metadata\Builder;

use Hofff\JsonLd\Metadata\Property;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class SerializableProperty implements Property
{
    /**
     * @internal
     *
     * @var string
     */
    public $name;

    /**
     * @internal
     *
     * @var string
     */
    public $accessor;

    /**
     * @internal
     *
     * @var string
     */
    public $mutator;

    /**
     * @internal
     *
     * @var string
     */
    public $iri;

    /**
     * @internal
     *
     * @var string
     */
    public $term;

    /**
     * @internal
     *
     * @var string|null
     */
    public $type;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAccessor(): string
    {
        return $this->accessor;
    }

    /**
     * @return string
     */
    public function getMutator(): string
    {
        return $this->mutator;
    }

    /**
     * @return string
     */
    public function getIri(): string
    {
        return $this->iri;
    }

    /**
     * @return string
     */
    public function getTerm(): string
    {
        return $this->term;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }
}
