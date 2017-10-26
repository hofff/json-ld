<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Test\Fixtures;

use Hofff\JsonLd\Annotation as JsonLd;

/**
 * @JsonLd\Vocab("https://example.com/second-alternating-related-model#")
 *
 * @author Oliver Hoff <oliver@hofff.com>
 */
class SecondAlternatingRelatedModel
{
    /**
     * @var FirstAlternatingRelatedModel|null
     */
    public $first = null;
}
