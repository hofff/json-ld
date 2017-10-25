<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Test\Serializer\Normalizer\JsonLdNormalizer;

use Doctrine\Common\Annotations\AnnotationReader;
use Hofff\JsonLd\Catalog\EmptyCatalog;
use Hofff\JsonLd\Metadata\Loader\AnnotationLoader;
use Hofff\JsonLd\Metadata\Provider\LoaderMetadataProvider;
use Hofff\JsonLd\Serializer\Normalizer\JsonLdNormalizer;
use Hofff\JsonLd\Test\Fixtures\SimpleModel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader as SerializerAnnotationLoader;

/**
 * Adds the support of an extra $context parameter for the supportsDecoding method.
 *
 * @author Oliver Hoff <oliver@hofff.com>
 */
class SimpleModelTest extends TestCase
{
    public function testNormalization()
    {
        $reader = new AnnotationReader();

        $loader = new SerializerAnnotationLoader($reader);
        $factory = new ClassMetadataFactory($loader);

        $loader = new AnnotationLoader($reader);
        $provider = new LoaderMetadataProvider($loader);

        $catalog = new EmptyCatalog();

        $normalizer = new JsonLdNormalizer($factory, $provider, $catalog);

        $serializer = new Serializer();
        $normalizer->setSerializer($serializer);
        $normalizer->setNormalizer($serializer);

        $model = new SimpleModel();

        $objects = $normalizer->normalize($model);

        $subset = [
            'https://example.com/simple-model#string' => [
                ['@value' => 'string'],
            ],
            'https://example.com/simple-model#int' => [
                ['@value' => 42],
            ],
            'https://example.com/simple-model#float' => [
                ['@value' => 4.2],
            ],
            'https://example.com/simple-model#true' => [
                ['@value' => true],
            ],
            'https://example.com/simple-model#false' => [
                ['@value' => false],
            ],
            'https://example.com/simple-model#null' => [
                ['@value' => null],
            ],
        ];

//         var_dump($0objects);

        self::assertCount(1, $objects);
        self::assertArrayHasKey(0, $objects);
        self::assertArrayHasKey('@id', $objects[0]);
        self::assertArraySubset($subset, $objects[0], true);
    }
}
