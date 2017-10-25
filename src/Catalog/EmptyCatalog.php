<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Catalog;

use Hofff\JsonLd\Exception\InvalidArgument;
use Hofff\JsonLd\Exception\UnknownIri;
use Hofff\JsonLd\Exception\UnknownObject;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class EmptyCatalog implements Catalog
{
    /**
     */
    public function __construct()
    {
    }

    /**
     * {@inheritDoc}
     * @see \Hofff\JsonLd\Catalog\Catalog::isIndexed()
     */
    public function isIndexed(string $iri): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     * @see \Hofff\JsonLd\Catalog\Catalog::lookup()
     */
    public function lookup(string $iri)
    {
        throw new UnknownIri();
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

        throw new UnknownObject();
    }
}
