<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Metadata;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
interface Metadata
{
    /**
     * @return string
     */
    public function getClass(): string;

    /**
     * @return string[]|array
     */
    public function getTypes(): array;

    /**
     * @return Property[]|array
     */
    public function getProperties(): array;
}
