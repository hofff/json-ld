<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Test\Serializer\Normalizer\JsonLdNormalizer;

use Hofff\JsonLd\Serializer\Normalizer\JsonLdNormalizer;
use Hofff\JsonLd\Test\Fixtures\LimitedRelatedModel;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class LimitedRelatedModelTest extends AbstractFunctionalTest
{
    /**
     * @var LimitedRelatedModel
     */
    protected $parent;

    /**
     * @var LimitedRelatedModel
     */
    protected $child;

    /**
     * @var LimitedRelatedModel
     */
    protected $grandchild;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        parent::setUp();

        $parent = new LimitedRelatedModel();
        $child = new LimitedRelatedModel();
        $grandchild = new LimitedRelatedModel();
        $greatgrandchild = new LimitedRelatedModel();

        $parent->related = $child;
        $child->related = $grandchild;
        $grandchild->related = $greatgrandchild;

        $this->catalog->add('https://example.com/limited-related-model/parent', $parent);
        $this->catalog->add('https://example.com/limited-related-model/child', $child);
        $this->catalog->add('https://example.com/limited-related-model/grandchild', $grandchild);
        $this->catalog->add('https://example.com/limited-related-model/greatgrandchild', $greatgrandchild);

        $this->parent = $parent;
        $this->child = $child;
        $this->grandchild = $grandchild;
    }

    public function testParent(): void
    {
        $context = [];
        $context[JsonLdNormalizer::KEY_ENABLE_MAX_DEPTH] = true;

        $objects = $this->normalizer->normalize($this->parent, null, $context);

        $normalized = [
            [
                '@id' => 'https://example.com/limited-related-model/parent',
                'https://example.com/limited-related-model#related' => [
                    [
                        '@id' => 'https://example.com/limited-related-model/child',
                        'https://example.com/limited-related-model#related' => [
                            ['@id' => 'https://example.com/limited-related-model/grandchild'],
                        ],
                    ],
                ],
            ],
        ];

        self::assertSame($normalized, $objects);
    }

    public function testChild(): void
    {
        $context = [];
        $context[JsonLdNormalizer::KEY_ENABLE_MAX_DEPTH] = true;

        $objects = $this->normalizer->normalize($this->child, null, $context);

        $normalized = [
            [
                '@id' => 'https://example.com/limited-related-model/child',
                'https://example.com/limited-related-model#related' => [
                    [
                        '@id' => 'https://example.com/limited-related-model/grandchild',
                        'https://example.com/limited-related-model#related' => [
                            ['@id' => 'https://example.com/limited-related-model/greatgrandchild'],
                        ],
                    ],
                ],
            ],
        ];

        self::assertSame($normalized, $objects);
    }

    public function testGrandchild(): void
    {
        $context = [];
        $context[JsonLdNormalizer::KEY_ENABLE_MAX_DEPTH] = true;

        $objects = $this->normalizer->normalize($this->grandchild, null, $context);

        $normalized = [
            [
                '@id' => 'https://example.com/limited-related-model/grandchild',
                'https://example.com/limited-related-model#related' => [
                    [
                        '@id' => 'https://example.com/limited-related-model/greatgrandchild',
                        'https://example.com/limited-related-model#related' => [
                            ['@value' => null],
                        ],
                    ],
                ],
            ],
        ];

        self::assertSame($normalized, $objects);
    }
}
