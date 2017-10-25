<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Metadata\Loader;

use Hofff\JsonLd\Metadata\Builder\MetadataBuilder;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
interface Loader
{
    /**
     * @param MetadataBuilder $builder
     *
     * @return bool
     */
    public function load(MetadataBuilder $builder): bool;
}
