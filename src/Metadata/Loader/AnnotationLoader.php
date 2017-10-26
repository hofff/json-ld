<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Metadata\Loader;

use Doctrine\Common\Annotations\Reader;
use Hofff\JsonLd\Annotation\Property;
use Hofff\JsonLd\Annotation\Term;
use Hofff\JsonLd\Annotation\Type;
use Hofff\JsonLd\Annotation\Vocab;
use Hofff\JsonLd\Exception\InvalidMetadataConfiguration;
use Hofff\JsonLd\Metadata\Builder\MetadataBuilder;
use Hofff\JsonLd\Metadata\Builder\PropertyBuilder;
use Hofff\JsonLd\Exception\InvalidArgument;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class AnnotationLoader implements Loader
{
    const CLASS_ANNOTATIONS = [
        Property::class,
        Term::class,
        Type::class,
        Vocab::class,
    ];

    const PROPERTY_ANNOTATIONS = [
        Property::class,
    ];

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritDoc}
     * @see \Hofff\JsonLd\Metadata\Loader\Loader::load()
     */
    public function load(MetadataBuilder $builder): bool
    {
        $loaded = false;
        $loaded = $this->loadClassAnnotations($builder) || $loaded;
        $loaded = $this->loadPropertyAnnotations($builder) || $loaded;

        return $loaded;
    }

    /**
     * @param MetadataBuilder $builder
     * @return bool
     */
    private function loadClassAnnotations(MetadataBuilder $builder): bool
    {
        $reflClass = $builder->getReflectionClass();
        $loaded = false;

        $classAnnotations = $this->createAnnotationMap(
            $this->reader->getClassAnnotations($reflClass),
            self::CLASS_ANNOTATIONS
        );

        foreach($classAnnotations[Property::class] ?? [] as $property) {
            /* @var Property $property */
            if($property->name === null) {
                throw new InvalidMetadataConfiguration();
            }

            $this->configurePropertyBuilder($builder->getPropertyBuilder($property->name), $property);

            $loaded = true;
        }

        foreach($classAnnotations[Term::class] ?? [] as $term) {
            /* @var Term $term */
            $builder->addTerm($term->name, $term->iri);

            $loaded = true;
        }

        foreach($classAnnotations[Type::class] ?? [] as $type) {
            /* @var Type $type */
            $builder->addType($type->iri ?? $reflClass->getShortName());

            $loaded = true;
        }

        foreach($classAnnotations[Vocab::class] ?? [] as $vocab) {
            /* @var Vocab $vocab */
            $builder->setVocab($vocab->iri);

            $loaded = true;
        }

        return $loaded;
    }

    /**
     * @param MetadataBuilder $builder
     * @throws InvalidMetadataConfiguration
     * @return bool
     */
    private function loadPropertyAnnotations(MetadataBuilder $builder): bool
    {
        $reflClass = $builder->getReflectionClass();
        $loaded = false;

        foreach ($reflClass->getProperties() as $reflProperty) {
            if ($reflProperty->getDeclaringClass()->getName() !== $reflClass->getName()) {
                continue;
            }

            $propertyName = $reflProperty->name;
            $propertyAnnotations = $this->createAnnotationMap(
                $this->reader->getPropertyAnnotations($reflProperty),
                self::PROPERTY_ANNOTATIONS
            );

            foreach($propertyAnnotations[Property::class] ?? [] as $property) {
                /* @var Property $property */
                if($property->name !== null && $property->name !== $propertyName) {
                    throw new InvalidMetadataConfiguration();
                }

                $this->configurePropertyBuilder($builder->getPropertyBuilder($propertyName), $property);

                $loaded = true;
            }
        }

        return $loaded;
    }

    /**
     * @param PropertyBuilder $builder
     * @param Property $property
     */
    private function configurePropertyBuilder(PropertyBuilder $builder, Property $property): void
    {
        try {
            $builder->setAccessor($property->accessor);
            $builder->setMutator($property->mutator);
            $builder->setIri($property->iri);
            $builder->setTerm($property->term);
            $builder->setType($property->type);
        } catch(InvalidArgument $e) {
            throw new InvalidMetadataConfiguration($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array $annotations
     * @param string[]|array $annotationClasses
     * @return array
     */
    private function createAnnotationMap(array $annotations, array $annotationClasses): array
    {
        $map = [];

        foreach ($annotations as $annotation) {
            foreach ($annotationClasses as $class) {
                if ($annotation instanceof $class) {
                    $map[$class][] = $annotation;
                    continue 2;
                }
            }
        }

        return $map;
    }
}
