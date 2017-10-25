<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Catalog;

use Hofff\JsonLd\Exception\InvalidArgument;
use Hofff\JsonLd\Exception\UnknownObject;
use Hofff\JsonLd\Exception\UnknownIri;
use Hofff\JsonLd\Exception\InvalidCatalogEntry;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class ArrayCatalog implements Catalog
{
    /**
     * @var array
     */
    private $iris;

    /**
     * @var array
     */
    private $objects;

    public function __construct()
    {
        $this->iris = [];
        $this->objects = [];
    }

    /**
     * @param string $iri
     * @param mixed $object
     * @param boolean $canonical
     * @throws InvalidArgument
     * @throws InvalidCatalogEntry
     */
    public function add(string $iri, $object, $canonical = true): void
    {
        if(!is_object($object)) {
            throw new InvalidArgument(sprintf('Argument #2 must be an object, got "%s"', gettype($object)));
        }

        if($this->isIndexed($iri)) {
            throw new InvalidCatalogEntry();
        }

        if($canonical && $this->contains($object)) {
            throw new InvalidCatalogEntry();
        }

        $this->iris[$iri] = $object;

        if($canonical) {
            $hash = spl_object_hash($object);
            $this->objects[$hash] = $iri;
        }
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
        try {
            $this->getIriOf($object);

            return true;
        } catch(UnknownObject $e) {
            return false;
        }
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

        if(!isset($this->objects[$hash])) {
            throw new UnknownObject();
        }

        return $this->objects[$hash];
    }
}
