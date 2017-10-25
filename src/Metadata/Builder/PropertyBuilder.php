<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Metadata\Builder;

use Hofff\JsonLd\Exception\InvalidMetadataConfiguration;
use Hofff\JsonLd\Metadata\Property;

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
     * @var string|null
     */
    private $accessor;

    /**
     * @var string|null
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
     * @return string|null
     */
    public function getAccessor(): ?string
    {
        return $this->accessor;
    }

    /**
     * @param string|null $accessor
     */
    public function setAccessor(?string $accessor): void
    {
        $this->accessor = $accessor;
    }

    /**
     * @return string|null
     */
    public function getMutator(): ?string
    {
        return $this->mutator;
    }

    /**
     * @param string|null $mutator
     */
    public function setMutator(?string $mutator): void
    {
        $this->mutator = $mutator;
    }

    /**
     * @return string|null
     */
    public function getIri(): ?string
    {
        return $this->iri;
    }

    /**
     * @param string|null $iri
     */
    public function setIri(?string $iri): void
    {
        $this->iri = $iri;
    }

    /**
     * @return string|null
     */
    public function getTerm(): ?string
    {
        return $this->term;
    }

    /**
     * @param string|null $term
     */
    public function setTerm(?string $term): void
    {
        $this->term = $term;
    }

    /**
     * @return string|null
     */
    public function getVocab(): ?string
    {
        return $this->vocab;
    }

    /**
     * @param string $iri
     */
    public function setVocab(?string $iri): void
    {
        $this->vocab = $iri;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
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
        $property = new SerializableProperty();

        if(null === $this->name) {
            throw new InvalidMetadataConfiguration();
        }

        $property->name = $this->name;
        $property->accessor = $this->accessor ?? $this->name;
        $property->mutator = $this->mutator ?? $this->name;
        $property->term = $this->term ?? $this->name;
        $property->type = $this->type;

        $property->iri = $this->resolveIri();

        return $property;
    }

    private function resolveIri(MetadataBuilder $builder): string
    {

        if($this->vocab !== null) {
            return $this->vocab.$this->name;
        }

        if(null !== $vocab = $builder->getVocab()) {
            return $vocab.$this->name;
        }

        throw new InvalidMetadataConfiguration();
    }
}
