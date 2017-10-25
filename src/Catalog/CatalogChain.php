<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Catalog;

use Hofff\JsonLd\Exception\InvalidArgument;
use Hofff\JsonLd\Exception\UnknownIri;
use Hofff\JsonLd\Exception\UnknownObject;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class CatalogChain implements Catalog
{
    /**
     * @var Catalog[]|array
     */
    private $catalogs;

    /**
     * @param Catalog[]|array|null $catalogs
     */
    public function __construct(?array $catalogs = null)
    {
        $this->catalogs = $catalogs ?? [];
    }

    /**
     * {@inheritDoc}
     * @see \Hofff\JsonLd\Catalog\Catalog::isIndexed()
     */
    public function isIndexed(string $iri): bool
    {
        foreach($this->catalogs as $catalog) {
            if($catalog->isIndexed($iri)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     * @see \Hofff\JsonLd\Catalog\Catalog::lookup()
     */
    public function lookup(string $iri)
    {
        foreach($this->catalogs as $catalog) {
            if($catalog->isIndexed($iri)) {
                return $catalog->lookup($iri);
            }
        }

        throw new UnknownIri();
    }

    /**
     * {@inheritDoc}
     * @see \Hofff\JsonLd\Catalog\Catalog::contains()
     */
    public function contains($object): bool
    {
        foreach($this->catalogs as $catalog) {
            if($catalog->contains($object)) {
                return true;
            }
        }

        return false;
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

        foreach($this->catalogs as $catalog) {
            if($catalog->contains($object)) {
                return $catalog->getIriOf($object);
            }
        }

        throw new UnknownObject();
    }
}
