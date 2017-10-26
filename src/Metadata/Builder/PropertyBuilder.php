<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Metadata\Builder;

use Hofff\JsonLd\Exception\InvalidMetadataConfiguration;
use Hofff\JsonLd\Metadata\Property;
use Hofff\JsonLd\Exception\InvalidArgument;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class PropertyBuilder
{
    /**
     * @var MetadataBuilder
     */
    private $metadataBuilder;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string|bool|null
     */
    private $accessor;

    /**
     * @var string|bool|null
     */
    private $mutator;

    /**
     * @var string|null
     */
    private $iri;

    /**
     * @var string|null
     */
    private $term;

    /**
     * @var string|null
     */
    private $vocab;

    /**
     * @var string|null
     */
    private $type;

    /**
     * @param MetadataBuilder $builder
     * @param string $name
     */
    public function __construct(MetadataBuilder $builder, string $name)
    {
        $this->metadataBuilder = $builder;
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|bool|null $accessor
     */
    public function setAccessor($accessor): void
    {
        if(!is_string($accessor) && !is_bool($accessor) && !is_null($accessor)) {
            throw new InvalidArgument();
        }

        $this->accessor = $accessor;
    }

    /**
     * @param string|bool|null $mutator
     */
    public function setMutator($mutator): void
    {
        if(!is_string($mutator) && !is_bool($mutator) && !is_null($mutator)) {
            throw new InvalidArgument();
        }

        $this->mutator = $mutator;
    }

    /**
     * @param string|null $iri
     */
    public function setIri(?string $iri): void
    {
        $this->iri = $iri;
    }

    /**
     * @param string|null $term
     */
    public function setTerm(?string $term): void
    {
        $this->term = $term;
    }

    /**
     * @param string $iri
     */
    public function setVocab(?string $iri): void
    {
        $this->vocab = $iri;
    }

    /**
     * @param string|null $iri
     */
    public function setType(?string $iri): void
    {
        $this->type = $iri;
    }

    /**
     * @return Property
     */
    public function build(): Property
    {
        if($this->accessor === false && $this->mutator === false) {
            throw new InvalidMetadataConfiguration();
        }

        $property = new SerializableProperty();

        $property->class = $this->metadataBuilder->getReflectionClass()->getName();
        $property->name = $this->name;
        $property->accessor = $this->accessor !== false ? is_string($this->accessor) ? $this->accessor : $this->name : null;
        $property->mutator = $this->mutator !== false ? is_string($this->mutator) ? $this->mutator : $this->name : null;
        $property->term = $this->term ?? $this->name;
        $property->type = $this->type;
        $property->iri = $this->resolveIri();

        return $property;
    }

    /**
     * @throws InvalidMetadataConfiguration
     * @return string
     */
    private function resolveIri(): string
    {
        if(null !== $vocab = $this->resolveVocab()) {
            return $vocab.$this->name;
        }

        throw new InvalidMetadataConfiguration();
    }

    /**
     * @return string|null
     */
    private function resolveVocab(): ?string
    {
        return $this->vocab ?? $this->metadataBuilder->resolveVocab();
    }
}
