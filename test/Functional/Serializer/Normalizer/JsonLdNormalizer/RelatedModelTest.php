<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Test\Functional\Serializer\Normalizer\JsonLdNormalizer;

use Hofff\JsonLd\Test\Fixtures\RelatedModel;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class RelatedModelTest extends AbstractFunctionalTest
{
    /**
     * @var RelatedModel
     */
    protected $parent;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        parent::setUp();

        $parent = new RelatedModel();
        $child = new RelatedModel();
        $grandchild = new RelatedModel();
        $greatgrandchild = new RelatedModel();

        $parent->related = $child;
        $child->related = $grandchild;
        $grandchild->related = $greatgrandchild;

        $this->catalog->add('https://example.com/related-model/parent', $parent);
        $this->catalog->add('https://example.com/related-model/child', $child);
        $this->catalog->add('https://example.com/related-model/grandchild', $grandchild);
        $this->catalog->add('https://example.com/related-model/greatgrandchild', $greatgrandchild);

        $this->parent = $parent;
    }

    public function testNormalization(): void
    {
        $objects = $this->normalizer->normalize($this->parent);

        $normalized = [
            [
                '@id' => 'https://example.com/related-model/parent',
                'https://example.com/related-model#related' => [
                    [
                        '@id' => 'https://example.com/related-model/child',
                        'https://example.com/related-model#related' => [
                            [
                                '@id' => 'https://example.com/related-model/grandchild',
                                'https://example.com/related-model#related' => [
                                    [
                                        '@id' => 'https://example.com/related-model/greatgrandchild',
                                        'https://example.com/related-model#related' => [
                                            ['@value' => null],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        self::assertSame($normalized, $objects);
    }
}
