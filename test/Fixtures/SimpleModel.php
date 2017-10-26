<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Test\Fixtures;

use Hofff\JsonLd\Annotation as JsonLd;
use Symfony\Component\Serializer\Annotation as Serial;

/**
 * @JsonLd\Vocab("https://example.com/simple-model#")
 *
 * @author Oliver Hoff <oliver@hofff.com>
 */
class SimpleModel
{
    /**
     * @Serial\Groups({"scalars"})
     *
     * @var string
     */
    public $string = 'string';

    /**
     * @Serial\Groups({"scalars", "numbers"})
     *
     * @var int
     */
    public $int = 42;

    /**
     * @Serial\Groups({"scalars", "numbers"})
     *
     * @var float
     */
    public $float = 4.2;

    /**
     * @Serial\Groups({"scalars", "bools"})
     *
     * @var bool
     */
    public $true = true;

    /**
     * @Serial\Groups({"scalars", "bools"})
     *
     * @var bool
     */
    public $false = false;

    /**
     * @var null
     */
    public $null = null;

    /**
     * @var mixed[]|array
     */
    public $mixed = [
        'string',
        42,
        4.2,
        true,
        false,
        null,
    ];
}
