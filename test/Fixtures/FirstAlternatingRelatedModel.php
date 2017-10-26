<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Test\Fixtures;

use Hofff\JsonLd\Annotation as JsonLd;
use Symfony\Component\Serializer\Annotation as Serial;

/**
 * @JsonLd\Vocab("https://example.com/first-alternating-related-model#")
 *
 * @author Oliver Hoff <oliver@hofff.com>
 */
class FirstAlternatingRelatedModel
{
    /**
     * @Serial\MaxDepth(2)
     *
     * @var SecondAlternatingRelatedModel|null
     */
    public $second = null;
}
