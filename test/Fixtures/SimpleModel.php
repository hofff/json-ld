<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Test\Fixtures;

use Hofff\JsonLd\Annotation as JsonLd;

/**
 * @JsonLd\Vocab("https://example.com/simple-model#")
 *
 * @author Oliver Hoff <oliver@hofff.com>
 */
class SimpleModel
{
    /**
     * @var string
     */
    public $string = 'string';

    /**
     * @var int
     */
    public $int = 42;

    /**
     * @var float
     */
    public $float = 4.2;

    /**
     * @var bool
     */
    public $true = true;

    /**
     * @var bool
     */
    public $false = false;

    /**
     * @var null
     */
    public $null = null;
}
