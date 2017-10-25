<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Annotation;

/**
 * @Annotation
 *
 * @author Oliver Hoff <oliver@hofff.com>
 */
final class Term
{
    /**
     * @Required
     *
     * @var string The name of the term
     */
    public $name;

    /**
     * @Required
     *
     * @var string The IRI this term maps to
     */
    public $iri;
}
