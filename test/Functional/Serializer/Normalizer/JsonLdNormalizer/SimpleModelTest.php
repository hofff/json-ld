<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Test\Functional\Serializer\Normalizer\JsonLdNormalizer;

use Hofff\JsonLd\Serializer\Normalizer\JsonLdNormalizer;
use Hofff\JsonLd\Test\Fixtures\NullModel;
use Hofff\JsonLd\Test\Fixtures\SimpleModel;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class SimpleModelTest extends AbstractFunctionalTest
{
    /**
     * @var SimpleModel
     */
    protected $model;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new SimpleModel();
        $zero = new NullModel();
        $one = new NullModel();

        array_unshift($this->model->mixed, $zero);
        array_push($this->model->mixed, $one);

        $this->catalog->add('https://example.com/simple-model/0', $this->model);
        $this->catalog->add('https://example.com/null-model/0', $zero);
        $this->catalog->add('https://example.com/null-model/1', $one);
    }

    public function testNormalization(): void
    {
        $objects = $this->normalizer->normalize($this->model);

        $normalized = [
            [
                '@id' => 'https://example.com/simple-model/0',
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
                'https://example.com/simple-model#mixed' => [
                    ['@id' => 'https://example.com/null-model/0'],
                    ['@value' => 'string'],
                    ['@value' => 42],
                    ['@value' => 4.2],
                    ['@value' => true],
                    ['@value' => false],
                    ['@value' => null],
                    ['@id' => 'https://example.com/null-model/1'],
                ],
            ],
        ];

        self::assertSame($normalized, $objects);
    }

    public function testGroups(): void
    {
        $context = [];
        $context[JsonLdNormalizer::GROUPS][] = 'numbers';

        $objects = $this->normalizer->normalize($this->model, null, $context);

        $normalized = [
            [
                '@id' => 'https://example.com/simple-model/0',
                'https://example.com/simple-model#int' => [
                    ['@value' => 42],
                ],
                'https://example.com/simple-model#float' => [
                    ['@value' => 4.2],
                ],
            ],
        ];

        self::assertSame($normalized, $objects);
    }

    public function testMultipleGroups(): void
    {
        $context = [];
        $context[JsonLdNormalizer::GROUPS][] = 'numbers';
        $context[JsonLdNormalizer::GROUPS][] = 'bools';

        $objects = $this->normalizer->normalize($this->model, null, $context);

        $normalized = [
            [
                '@id' => 'https://example.com/simple-model/0',
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
            ],
        ];

        self::assertSame($normalized, $objects);
    }

    public function testAttributes(): void
    {
        $context = [];
        $context[JsonLdNormalizer::ATTRIBUTES][] = 'string';
        $context[JsonLdNormalizer::ATTRIBUTES][] = 'float';
        $context[JsonLdNormalizer::ATTRIBUTES][] = 'true';

        $objects = $this->normalizer->normalize($this->model, null, $context);

        $normalized = [
            [
                '@id' => 'https://example.com/simple-model/0',
                'https://example.com/simple-model#string' => [
                    ['@value' => 'string'],
                ],
                'https://example.com/simple-model#float' => [
                    ['@value' => 4.2],
                ],
                'https://example.com/simple-model#true' => [
                    ['@value' => true],
                ],
            ],
        ];

        self::assertSame($normalized, $objects);
    }

    public function testAttributesAndGroups(): void
    {
        $context = [];
        $context[JsonLdNormalizer::ATTRIBUTES][] = 'string';
        $context[JsonLdNormalizer::ATTRIBUTES][] = 'float';
        $context[JsonLdNormalizer::ATTRIBUTES][] = 'true';
        $context[JsonLdNormalizer::GROUPS][] = 'numbers';

        $objects = $this->normalizer->normalize($this->model, null, $context);

        $normalized = [
            [
                '@id' => 'https://example.com/simple-model/0',
                'https://example.com/simple-model#float' => [
                    ['@value' => 4.2],
                ],
            ],
        ];

        self::assertSame($normalized, $objects);
    }
}
