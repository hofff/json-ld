<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Test\Functional\Serializer\Normalizer\JsonLdNormalizer;

use Hofff\JsonLd\Test\Fixtures\NullModel;
use Hofff\JsonLd\Test\Fixtures\IterableModel;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class IterableModelTest extends AbstractFunctionalTest
{
    /**
     * @var IterableModel
     */
    protected $model;

    /**
     * @var \ArrayObject
     */
    protected $modelWithoutMetadata;

    /**
     * {@inheritDoc}
     * @see \Hofff\JsonLd\Test\Functional\Serializer\Normalizer\JsonLdNormalizer\AbstractFunctionalTest::setUp()
     */
    protected function setUp(): void
    {
        parent::setUp();

        $zero = new NullModel();
        $one = new NullModel();
        $this->model = new IterableModel([$zero, $one]);
        $this->modelWithoutMetadata = new \ArrayObject([$zero, $one]);

        $this->catalog->add('https://example.com/iterable-model/0', $this->model);
        $this->catalog->add('https://example.com/null-model/0', $zero);
        $this->catalog->add('https://example.com/null-model/1', $one);
    }

    public function testModel(): void
    {
        $objects = $this->normalizer->normalize($this->model);

        $normalized = [
            [
                '@id' => 'https://example.com/iterable-model/0',
                'https://example.com/iterable-model#count' => [
                    ['@value' => 2],
                ],
                'https://example.com/iterable-model#items' => [
                    ['@id' => 'https://example.com/null-model/0'],
                    ['@id' => 'https://example.com/null-model/1'],
                ],
            ],
        ];

        self::assertSame($normalized, $objects);
    }

    public function testModelWithoutMetadata(): void
    {
        $objects = $this->normalizer->normalize($this->modelWithoutMetadata);

        $normalized = [
            ['@id' => 'https://example.com/null-model/0'],
            ['@id' => 'https://example.com/null-model/1'],
        ];

        self::assertSame($normalized, $objects);
    }
}
