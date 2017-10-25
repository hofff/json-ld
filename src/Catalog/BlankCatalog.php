<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Catalog;

use Hofff\JsonLd\Exception\InvalidArgument;
use Hofff\JsonLd\Exception\UnknownIri;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class BlankCatalog implements Catalog
{
    /**
     * @var array
     */
    private $iris;

    /**
     * @var array
     */
    private $objects;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @param string|null $prefix
     */
    public function __construct(?string $prefix = null)
    {
        $this->iris = [];
        $this->objects = [];
        $this->prefix = $prefix ?? spl_object_hash($this);
    }

    /**
     * {@inheritDoc}
     * @see \Hofff\JsonLd\Catalog\Catalog::isIndexed()
     */
    public function isIndexed(string $iri): bool
    {
        return isset($this->iris[$iri]);
    }

    /**
     * {@inheritDoc}
     * @see \Hofff\JsonLd\Catalog\Catalog::lookup()
     */
    public function lookup(string $iri)
    {
        if(!isset($this->iris[$iri])) {
            throw new UnknownIri();
        }

        return $this->iris[$iri];
    }

    /**
     * {@inheritDoc}
     * @see \Hofff\JsonLd\Catalog\Catalog::contains()
     */
    public function contains($object): bool
    {
        if(!is_object($object)) {
            throw new InvalidArgument(sprintf('Argument #1 must be an object, got "%s"', gettype($object)));
        }

        return true;
    }

    /**
     * {@inheritDoc}
     * @see \Hofff\JsonLd\Catalog\Catalog::getIriOf()
     */
    public function getIriOf($object): string
    {
        if(!is_object($object)) {
            throw new InvalidArgument(sprintf('Argument #1 must be an object, got "%s"', gettype($object)));
        }

        $hash = spl_object_hash($object);
        if(isset($this->objects[$hash])) {
            return $this->objects[$hash];
        }

        $iri = sprintf('_:blank_%s_%d', $this->prefix, count($this->objects) + 1);
        $this->objects[$hash] = $iri;
        $this->iris[$iri] = $object;

        return $iri;
    }
}
