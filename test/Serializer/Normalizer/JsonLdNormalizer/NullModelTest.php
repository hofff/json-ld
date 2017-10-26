<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Test\Serializer\Normalizer\JsonLdNormalizer;

use Hofff\JsonLd\Test\Fixtures\NullModel;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class NullModelTest extends AbstractFunctionalTest
{
    public function testNormalization(): void
    {
        $zero = new NullModel();

        $this->catalog->add('https://example.com/null-model/0', $zero);

        $objects = $this->normalizer->normalize($zero);

        $normalized = [
            ['@id' => 'https://example.com/null-model/0'],
        ];

        self::assertSame($normalized, $objects);
    }

    public function testBlank(): void
    {
        $model = new NullModel();

        $objects = $this->normalizer->normalize($model);

        self::assertCount(1, $objects);
        self::assertArrayHasKey(0, $objects);
        self::assertArrayHasKey('@id', $objects[0]);
        self::assertStringMatchesFormat('_:%a', $objects[0]['@id']);
    }

    public function testMultipleBlank(): void
    {
        $zero = new NullModel();
        $one = new NullModel();

        $objects = $this->normalizer->normalize([$zero, $one]);

        self::assertCount(2, $objects);
        self::assertArrayHasKey(0, $objects);
        self::assertArrayHasKey(1, $objects);
        self::assertArrayHasKey('@id', $objects[0]);
        self::assertArrayHasKey('@id', $objects[1]);
        self::assertStringMatchesFormat('_:%a', $objects[0]['@id']);
        self::assertStringMatchesFormat('_:%a', $objects[1]['@id']);
        self::assertNotSame($objects[0]['@id'], $objects[1]['@id']);
    }

    public function testMultipleIdenticalBlank(): void
    {
        $zero = new NullModel();

        $objects = $this->normalizer->normalize([$zero, $zero]);

        self::assertCount(2, $objects);
        self::assertArrayHasKey(0, $objects);
        self::assertArrayHasKey(1, $objects);
        self::assertArrayHasKey('@id', $objects[0]);
        self::assertArrayHasKey('@id', $objects[1]);
        self::assertStringMatchesFormat('_:%a', $objects[0]['@id']);
        self::assertStringMatchesFormat('_:%a', $objects[1]['@id']);
        self::assertSame($objects[0]['@id'], $objects[1]['@id']);
    }

    public function testMixed(): void
    {
        $zero = new NullModel();
        $one = new NullModel();

        $this->catalog->add('https://example.com/null-model/0', $zero);

        $objects = $this->normalizer->normalize([$zero, $one]);

        $normalizedZero = ['@id' => 'https://example.com/null-model/0'];

        self::assertCount(2, $objects);
        self::assertArrayHasKey(0, $objects);
        self::assertArrayHasKey(1, $objects);
        self::assertSame($normalizedZero, $objects[0]);
        self::assertArrayHasKey('@id', $objects[1]);
        self::assertStringMatchesFormat('_:%a', $objects[1]['@id']);
    }
}
