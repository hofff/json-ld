<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Metadata\Provider;

use Hofff\JsonLd\Exception\InvalidArgument;
use Hofff\JsonLd\Metadata\Metadata;
use Hofff\JsonLd\Metadata\Builder\MetadataBuilder;
use Hofff\JsonLd\Metadata\Loader\Loader;
use Hofff\JsonLd\Exception\MetadataUnavailable;

/**
 * Provides metadata from configuration loaded by a Loader
 *
 * @author Oliver Hoff <oliver@hofff.com>
 */
class LoaderMetadataProvider implements MetadataProvider
{
    use ClassNameResolverTrait;

    /**
     * @var MetadataBuilder[]|array
     */
    private $builders;

    /**
     * @var Metadata[]|array
     */
    private $metadata;

    /**
     * @var bool[]|array
     */
    private $available;

    /**
     * @var Loader
     */
    private $loader;

    /**
     * @param Loader $loader
     */
    public function __construct(Loader $loader)
    {
        $this->builders = [];
        $this->metadata = [];
        $this->available = [];
        $this->loader = $loader;
    }

    /**
     * {@inheritDoc}
     * @see \Hofff\JsonLd\Metadata\Provider\MetadataProvider::getMetadataFor()
     */
    public function getMetadataFor($value): Metadata
    {
        $className = $this->resolveClassName($value);

        if (isset($this->metadata[$className])) {
            return $this->metadata[$className];
        }

        $builder = $this->createBuilder($className);

        if (!$this->available[$className]) {
            throw new MetadataUnavailable();
        }

        return $this->metadata[$className] = $builder->build();
    }

    /**
     * {@inheritDoc}
     * @see \Hofff\JsonLd\Metadata\Provider\MetadataProvider::isMetadataAvailableFor()
     */
    public function isMetadataAvailableFor($value): bool
    {
        try {
            $className = $this->resolveClassName($value);
        } catch (InvalidArgument $e) {
            return false;
        }

        $this->createBuilder($className);

        return $this->available[$className];
    }

    /**
     * @param string $className
     *
     * @return MetadataBuilder
     */
    private function createBuilder(string $className): MetadataBuilder
    {
        if (isset($this->builders[$className])) {
            return $this->builders[$className];
        }

        $builder = MetadataBuilder::create($className);
        $class = $builder->getReflectionClass();

        if ($parent = $class->getParentClass()) {
            $builder->setParentClassBuilder($this->createBuilder($parent->name));
        }

        foreach ($class->getInterfaces() as $interface) {
            $builder->addInterfaceBuilder($this->createBuilder($interface->name));
        }

        $this->available[$className] = $this->loader->load($builder);
        $this->builders[$className] = $builder;

        return $builder;
    }
}
