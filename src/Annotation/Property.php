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
     * @var string The name of the property. If this annotation is used on a property of a class, the names should match.
     */
    public $name;

    /**
     * @var mixed The accessor method to use to retrieve the property from an object
     */
    public $accessor;

    /**
     * @var mixed The mutator method to use to set the property on an object
     */
    public $mutator;

    /**
     * @var string The IRI identifying this property
     */
    public $iri;

    /**
     * @var string The preferred term name of this property to be used in context definitions
     */
    public $term;

    /**
     * @var string The IRI identifying the type of this property
     */
    public $type;
}
