<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Metadata;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
interface Property
{
    /**
     * @return string
     */
    public function getClass(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getAccessor(): ?string;

    /**
     * @return string
     */
    public function getMutator(): ?string;

    /**
     * @return string
     */
    public function getIri(): string;

    /**
     * @return string
     */
    public function getTerm(): string;

    /**
     * @return string|null
     */
    public function getType(): ?string;
}
