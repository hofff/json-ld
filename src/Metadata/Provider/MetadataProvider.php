<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Metadata\Provider;

use Hofff\JsonLd\Exception\MetadataUnavailable;
use Hofff\JsonLd\Metadata\Metadata;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
interface MetadataProvider
{
    /**
     * @param string|object $value
     *
     * @throws MetadataUnavailable
     *
     * @return Metadata
     */
    public function getMetadataFor($value): Metadata;

    /**
     * @param string|object $value
     *
     * @return bool
     */
    public function isMetadataAvailableFor($value): bool;
}
