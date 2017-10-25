<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Metadata\Provider;

use Hofff\JsonLd\Exception\InvalidArgument;

/**
 * @internal
 *
 * @author Oliver Hoff <oliver@hofff.com>
 */
trait ClassNameResolverTrait
{
    /**
     * @param string|object $value
     *
     * @throws InvalidArgument
     *
     * @return string
     */
    private function resolveClassName($value): string
    {
        if (is_object($value)) {
            return get_class($value);
        }

        if (!is_string($value)) {
            throw new InvalidArgument(sprintf('Expected string or object, got: "%s"', gettype($value)));
        }

        if (!class_exists($value) && !interface_exists($value)) {
            throw new InvalidArgument(sprintf('The class or interface "%s" does not exist.', $value));
        }

        return ltrim($value, '\\');
    }
}
