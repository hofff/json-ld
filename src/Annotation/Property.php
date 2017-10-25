<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Annotation;

/**
 * @Annotation
 *
 * @author Oliver Hoff <oliver@hofff.com>
 */
final class Property
{
    /**
     * @var string|null The name of the property. If this annotation is used on a property of a class, the names should match.
     */
    public $name;

    /**
     * @var string|bool|null The accessor method to use to retrieve the property from an object
     */
    public $accessor;

    /**
     * @var string|bool|null The mutator method to use to set the property on an object
     */
    public $mutator;

    /**
     * @var string|null The IRI identifying this property
     */
    public $iri;

    /**
     * @var string|null The preferred term name of this property to be used in context definitions
     */
    public $term;

    /**
     * @var string|null The IRI identifying the type of this property
     */
    public $type;
}
