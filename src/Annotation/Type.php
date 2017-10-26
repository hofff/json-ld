<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Annotation;

/**
 * @Annotation
 *
 * @author Oliver Hoff <oliver@hofff.com>
 */
final class Type
{
    /**
     * @var string The node type of objects serialized from this class instances
     */
    public $iri;
}
