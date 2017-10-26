<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Serializer\Normalizer;

use Hofff\JsonLd\Catalog\ArrayCacheCatalogDecorator;
use Hofff\JsonLd\Catalog\BlankCatalog;
use Hofff\JsonLd\Catalog\Catalog;
use Hofff\JsonLd\Catalog\CatalogChain;
use Hofff\JsonLd\Exception\NotSupported;
use Hofff\JsonLd\Exception\TopLevelValueObjectsNotAllowed;
use Hofff\JsonLd\Metadata\Provider\MetadataProvider;
use Hofff\JsonLd\Serializer\Constants;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Hofff\JsonLd\Metadata\Metadata;
use Hofff\JsonLd\Metadata\Property;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class JsonLdNormalizer extends AbstractNormalizer implements NormalizerAwareInterface
{
    const KEY_ENABLE_MAX_DEPTH = 'enable_max_depth';
    const KEY_PROPERTY_STACK = 'hofff_jsonld_property_stack';
    const KEY_CATALOG = 'hofff_jsonld_catalog';
//     private const KEY_SKIP = 'hofff_jsonld_skip';
    private const KEY_CONTEXT_CATALOG = 'hofff_jsonld_context_catalog';

    use NormalizerAwareTrait;

    /**
     * @var MetadataProvider
     */
    private $metadataProvider;

    /**
     * @var Catalog
     */
    private $catalog;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @param ClassMetadataFactoryInterface $classMetadataFactory
     * @param MetadataProvider $metadataProvider
     * @param Catalog $catalog
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(
        ClassMetadataFactoryInterface $classMetadataFactory,
        MetadataProvider $metadataProvider,
        Catalog $catalog,
        ?PropertyAccessorInterface $propertyAccessor = null
    )
    {
        parent::__construct($classMetadataFactory);

        $this->metadataProvider = $metadataProvider;
        $this->catalog = $catalog;
        $this->propertyAccessor = $propertyAccessor ?? PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Serializer\Normalizer\NormalizerInterface::supportsNormalization()
     */
    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        return $format === Constants::FORMAT || $format === Constants::MIME_TYPE;
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Serializer\Normalizer\NormalizerInterface::normalize()
     */
    public function normalize($things, $format = null, array $context = [])
    {
        $context = $this->initializeContext($context);

        if(
            !is_iterable($things)
            || (is_object($things) && $this->metadataProvider->isMetadataAvailableFor($things))
        ) {
            $things = [$things];
        }

        $objects = [];
        foreach($things as $thing) {
            if($this->isValue($thing)) {
                $objects[] = $this->createValueObject($thing, $context);

                continue;
            }

            if(is_object($thing) && $this->metadataProvider->isMetadataAvailableFor($thing)) {
                $objects[] = $this->createNodeObject($thing, $context);

                continue;
            }

            $thing = $this->normalizer->normalize($thing, 'json', $context);
            if($this->isValue($thing)) {
                $objects[] = $this->createValueObject($thing, $context);

                continue;
            }

            throw new UnexpectedValueException();
        }

        return $objects;
    }

    /**
     * @param array $context
     * @return array
     */
    private function initializeContext(array $context): array
    {
        if(isset($context[self::KEY_CONTEXT_CATALOG])) {
            return $context;
        }

        $context[self::KEY_PROPERTY_STACK] = [];
        $context[self::KEY_CONTEXT_CATALOG] = $this->createContextCatalog($context[self::KEY_CATALOG] ?? null);

        return $context;
    }

    /**
     * @return Catalog
     */
    private function createContextCatalog(?Catalog $catalog): Catalog
    {
        $catalogs = [$this->catalog, new BlankCatalog()];
        $catalog && array_unshift($catalogs, $catalog);
        $catalog = new CatalogChain($catalogs);
        $catalog = new ArrayCacheCatalogDecorator($catalog);

        return $catalog;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private function isValue($value): bool
    {
        return $value === null || is_scalar($value);
    }

    /**
     * @param string|int|float|bool|null $value
     * @param array $context
     * @throws TopLevelValueObjectsNotAllowed
     * @return array
     */
    private function createValueObject($thing, array $context): array
    {
        if(!$property = $this->getCurrentProperty($context)) {
            throw new TopLevelValueObjectsNotAllowed();
        }

        $object = [];

        if($type = $property->getType()) {
            $object['@value'] = (string) $thing;
            $object['@type'] = $type;
        } else {
            $object['@value'] = $thing;
        }

        return $object;
    }

    /**
     * @param object $value
     * @param array $context
     * @return array
     */
    private function createNodeObject($thing, array $context): array
    {
        $object = [];
        $object['@id'] = $context[self::KEY_CONTEXT_CATALOG]->getIriOf($thing);

        if($this->isCircularReference($thing, $context)) {
            return $object;
        }

        if($this->isMaxDepthReached($context)) {
            return $object;
        }

        $metadata = $this->metadataProvider->getMetadataFor($thing);

        if($types = $metadata->getTypes()) {
            $object['@type'] = $types;
        }

        foreach($this->getProperties($metadata, $context) as $property) {
            /* @var Property $property */
            $value = $this->propertyAccessor->getValue($thing, $property->getAccessor());

            $childContext = $this->createChildContext($context, $property->getName());
            $childContext[self::KEY_PROPERTY_STACK][] = $property;

            $object[$property->getIri()] = $this->normalize($value, Constants::FORMAT, $childContext);
        }

        return $object;
    }

    /**
     * @param array $context
     * @return bool
     */
    private function isMaxDepthReached(array &$context): bool
    {
        if(empty($context[self::KEY_ENABLE_MAX_DEPTH])) {
            return false;
        }

        if(!$currentProperty = $this->getCurrentProperty($context)) {
            return false;
        }

        $class = $currentProperty->getClass();
        $propertyName = $currentProperty->getName();
        $attributesMetadata = $this->classMetadataFactory->getMetadataFor($class)->getAttributesMetadata();
        if (!isset($attributesMetadata[$propertyName])) {
            return false;
        }

        if(null === $maxDepth = $attributesMetadata[$propertyName]->getMaxDepth()) {
            return false;
        }

        $depth = 0;
        foreach($context[self::KEY_PROPERTY_STACK] as $property) {
            $depth += $property === $currentProperty;
        }

        return $depth >= $maxDepth;
    }

    /**
     * @param Metadata $metadata
     * @param array $context
     * @return Property[]|array
     */
    private function getProperties(Metadata $metadata, array $context): array
    {
        $class = $metadata->getClass();
        $properties = $metadata->getProperties();

        if(false !== $allowedAttributes = $this->getAllowedAttributes($class, $context, true)) {
            $allowedAttributes = array_flip($allowedAttributes);
            $properties = array_filter($properties, function(Property $property) use($allowedAttributes) {
                return isset($allowedAttributes[$property->getName()]);
            });
        } else {
            $properties = array_filter($properties, function(Property $property) use($class, $context) {
                return $this->isAllowedAttribute($class, $property->getName(), Constants::FORMAT, $context);
            });
        }

        return $properties;
    }

    /**
     * @param array $context
     * @return Property|null
     */
    private function getCurrentProperty(array $context): ?Property
    {
        if(!$top = count($context[self::KEY_PROPERTY_STACK])) {
            return null;
        }

        return $context[self::KEY_PROPERTY_STACK][$top - 1];
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Serializer\Normalizer\DenormalizerInterface::supportsDenormalization()
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Serializer\Normalizer\DenormalizerInterface::denormalize()
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        throw new NotSupported();
    }
}
