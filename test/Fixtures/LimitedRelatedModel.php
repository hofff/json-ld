<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Test\Fixtures;

use Hofff\JsonLd\Annotation as JsonLd;
use Symfony\Component\Serializer\Annotation as Serial;

/**
 * @JsonLd\Vocab("https://example.com/limited-related-model#")
 *
 * @author Oliver Hoff <oliver@hofff.com>
 */
class LimitedRelatedModel
{
    /**
     * @Serial\MaxDepth(2)
     *
     * @var LimitedRelatedModel|null
     */
    public $related = null;
}
