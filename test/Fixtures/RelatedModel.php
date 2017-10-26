<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Test\Fixtures;

use Hofff\JsonLd\Annotation as JsonLd;

/**
 * @JsonLd\Vocab("https://example.com/related-model#")
 *
 * @author Oliver Hoff <oliver@hofff.com>
 */
class RelatedModel
{
    /**
     *
     * @var RelatedModel|null
     */
    public $related = null;
}
