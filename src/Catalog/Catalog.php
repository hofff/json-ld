<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Catalog;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
interface Catalog
{
    /**
     * @param string $iri
     * @return bool
     */
    public function isIndexed(string $iri): bool;

    /**
     * @param string $iri
     * @return object
     */
    public function lookup(string $iri);

    /**
     * @param object $object
     * @return bool
     */
    public function contains($object): bool;

    /**
     * @param object $object
     * @return string
     */
    public function getIriOf($object): string;
}
