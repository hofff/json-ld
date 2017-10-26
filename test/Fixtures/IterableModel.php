<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Test\Fixtures;

use Hofff\JsonLd\Annotation as JsonLd;

/**
 * @JsonLd\Vocab("https://example.com/iterable-model#")
 * @JsonLd\Property("count", mutator=false)
 *
 * @author Oliver Hoff <oliver@hofff.com>
 */
class IterableModel implements \IteratorAggregate, \Countable
{
    /**
     * @JsonLd\Property(accessor="iterator", mutator=false)
     *
     * @var array
     */
    private $items;

    /**
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * {@inheritDoc}
     * @see \IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * {@inheritDoc}
     * @see \Countable::count()
     */
    public function count()
    {
        return count($this->items);
    }
}
