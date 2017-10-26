<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Metadata\Builder;

use Hofff\JsonLd\Metadata\Metadata;
use Hofff\JsonLd\Exception\InvalidArgument;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class MetadataBuilder
{
    /**
     * @param string $class
     * @return self
     */
    public static function create(string $class): self
    {
        $reflClass = new \ReflectionClass($class);

        return new self($reflClass);
    }

    /**
     * @var \ReflectionClass
     */
    private $reflClass;

    /**
     * @var self|null
     */
    private $parentClassBuilder;

    /**
     * @var self[]|null
     */
    private $interfaceBuilders;

    /**
     * @var string[]|array
     */
    private $terms;

    /**
     * @var string[]|array
     */
    private $types;

    /**
     * @var string
     */
    private $vocab;

    /**
     * @var PropertyBuilder[]|array
     */
    private $propertyBuilders;

    /**
     * @param \ReflectionClass $reflClass
     */
    public function __construct(\ReflectionClass $reflClass)
    {
        $this->reflClass = $reflClass;
        $this->interfaceBuilders = [];
        $this->types = [];
        $this->propertyBuilders = [];
    }

    /**
     * @return \ReflectionClass
     */
    public function getReflectionClass(): \ReflectionClass
    {
        return $this->reflClass;
    }

    /**
     * @param self $builder
     */
    public function setParentClassBuilder(self $builder): void
    {
        $this->parentClassBuilder = $builder;
    }

    /**
     * @param self $builder
     * @throws InvalidArgument
     */
    public function addInterfaceBuilder(self $builder): void
    {
        $name = $builder->getReflectionClass()->name;
        if(isset($this->interfaceBuilders[$name])) {
            throw new InvalidArgument();
        }

        $this->interfaceBuilders[$name] = $builder;
    }

    /**
     * @param string $term
     * @param string $iri
     */
    public function addTerm(string $term, string $iri): void
    {
        $this->terms[$term] = $iri;
    }

    /**
     * @param string $iri
     */
    public function addType(string $iri): void
    {
        $this->types[] = $iri;
    }

    /**
     * @param string $iri
     */
    public function setVocab(?string $iri): void
    {
        $this->vocab = $iri;

        if($iri === null) {
            unset($this->terms['vocab']);
        } else {
            $this->terms['vocab'] = $iri;
        }
    }

    /**
     * @param string $name
     * @return PropertyBuilder
     */
    public function getPropertyBuilder(string $name): PropertyBuilder
    {
        if(isset($this->propertyBuilders[$name])) {
            return $this->propertyBuilders[$name];
        }

        $builder = new PropertyBuilder($this, $name);

        return $this->propertyBuilders[$name] = $builder;
    }

    /**
     * @return Metadata
     */
    public function build(): Metadata
    {
        $class = $this->reflClass->getName();

        $metadata = new SerializableMetadata();
        $metadata->class = $class;
        $metadata->types = [];
        $metadata->properties = [];

        foreach($this->types as $type) {
            $metadata->types[] = $type;
        }

        if($this->vocab !== null) {
            foreach ($this->reflClass->getProperties(\ReflectionProperty::IS_PUBLIC) as $reflProperty) {
                /* @var \ReflectionProperty $reflProperty */
                $propertyName = $reflProperty->getName();
                if (isset($metadata->properties[$propertyName])) {
                    continue;
                }
                if ($reflProperty->getDeclaringClass()->getName() !== $class) {
                    continue;
                }

                $property = new SerializableProperty();
                $property->class = $class;
                $property->name = $propertyName;
                $property->accessor = $propertyName;
                $property->mutator = $propertyName;
                $property->iri = $this->vocab.$propertyName;
                $property->term = $propertyName;

                $metadata->properties[$propertyName] = $property;
            }
        }

        return $metadata;
    }
}
