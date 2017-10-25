<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Metadata\Loader;

use Hofff\JsonLd\Metadata\Builder\MetadataBuilder;
use Hofff\JsonLd\Exception\InvalidArgument;

/**
 * Calls multiple {@link Loader} instances in a chain.
 *
 * This class accepts multiple instances of LoaderInterface to be passed to the
 * constructor. When {@link load()} is called, the same method is called
 * in <em>all</em> of these loaders, regardless of whether any of them was
 * successful or not.
 *
 * @author Oliver Hoff <oliver@hofff.com>
 */
class LoaderChain implements Loader
{
    /**
     * @var Loader[]|array
     */
    private $loaders;

    /**
     * @throws InvalidArgument If any of the loaders does not implement the interface Loader
     *
     * @param Loader[]|array $loaders
     */
    public function __construct(array $loaders)
    {
        foreach ($loaders as $loader) {
            if (!$loader instanceof Loader) {
                throw new InvalidArgument(sprintf('Class "%s" is expected to implement "%s"', get_class($loader), Loader::class));
            }
        }

        $this->loaders = $loaders;
    }

    /**
     * {@inheritDoc}
     * @see \Hofff\JsonLd\Metadata\Loader\Loader::load()
     */
    public function load(MetadataBuilder $builder): bool
    {
        $success = false;

        foreach ($this->loaders as $loader) {
            $success = $loader->load($builder) || $success;
        }

        return $success;
    }
}
