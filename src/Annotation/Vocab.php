<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Annotation;

/**
 * @Annotation
 *
 * @author Oliver Hoff <oliver@hofff.com>
 */
final class Vocab
{
    /**
     * @Required
     *
     * @var string The IRI identifying the vocabulary
     */
    public $iri;
}
