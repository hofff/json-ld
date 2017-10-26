<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Test\Serializer\Normalizer\JsonLdNormalizer;

use Doctrine\Common\Annotations\AnnotationReader;
use Hofff\JsonLd\Catalog\ArrayCatalog;
use Hofff\JsonLd\Metadata\Loader\AnnotationLoader;
use Hofff\JsonLd\Metadata\Provider\LoaderMetadataProvider;
use Hofff\JsonLd\Serializer\Normalizer\JsonLdNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader as SerializerAnnotationLoader;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
abstract class AbstractFunctionalTest extends TestCase
{
    /**
     * @var ArrayCatalog
     */
    protected $catalog;

    /**
     * @var JsonLdNormalizer
     */
    protected $normalizer;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $reader = new AnnotationReader();

        $loader = new SerializerAnnotationLoader($reader);
        $factory = new ClassMetadataFactory($loader);

        $loader = new AnnotationLoader($reader);
        $provider = new LoaderMetadataProvider($loader);

        $this->catalog = new ArrayCatalog();

        $normalizer = new JsonLdNormalizer($factory, $provider, $this->catalog);

        $serializer = new Serializer();
        $normalizer->setSerializer($serializer);
        $normalizer->setNormalizer($serializer);

        $this->normalizer = $normalizer;
    }
}
