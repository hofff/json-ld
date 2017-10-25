<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Catalog;

use Hofff\JsonLd\Exception\InvalidArgument;
use Hofff\JsonLd\Exception\UnknownIri;
use Hofff\JsonLd\Exception\UnknownObject;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class ArrayCacheCatalogDecorator implements Catalog
{
    /**
     * @var array
     */
    private $isIndexedCache;

    /**
     * @var array
     */
    private $lookupCache;

    /**
     * @var array
     */
    private $containsCache;

    /**
     * @var array
     */
    private $getIriOfCache;

    /**
     * @var Catalog
     */
    private $decorated;

    public function __construct(Catalog $decorated)
    {
        $this->isIndexedCache = [];
        $this->lookupCache = [];
        $this->containsCache = [];
        $this->getIriOfCache = [];
        $this->decorated = $decorated;
    }

    /**
     * {@inheritDoc}
     * @see \Hofff\JsonLd\Catalog\Catalog::isIndexed()
     */
    public function isIndexed(string $iri): bool
    {
        return $this->isIndexedCache[$iri] ?? $this->isIndexedCache[$iri] = $this->decorated->isIndexed($iri);
    }

    /**
     * {@inheritDoc}
     * @see \Hofff\JsonLd\Catalog\Catalog::lookup()
     */
    public function lookup(string $iri)
    {
        if(!isset($this->lookupCache[$iri])) {
            try {
                $this->lookupCache[$iri] = $this->decorated->lookup($iri);
            } catch(UnknownIri $e) {
                $this->lookupCache[$iri] = $e;
            }
        }

        $object = $this->lookupCache[$iri];
        if($object instanceof UnknownIri) {
            throw new UnknownIri($object->getMessage(), $object->getCode(), $object);
        }

        return $object;
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

        $hash = spl_object_hash($object);

        return $this->containsCache[$hash] ?? $this->containsCache[$hash] = $this->decorated->contains($object);
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

        if(!isset($this->getIriOfCache[$hash])) {
            try {
                $this->getIriOfCache[$hash] = $this->decorated->getIriOf($object);
            } catch(UnknownObject $e) {
                $this->getIriOfCache[$hash] = $e;
            }
        }

        $iri = $this->getIriOfCache[$hash];
        if($iri instanceof UnknownObject) {
            throw new UnknownObject($iri->getMessage(), $iri->getCode(), $iri);
        }

        return $iri;
    }
}
