<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Test\Functional\Serializer\Normalizer\JsonLdNormalizer;

use Hofff\JsonLd\Test\Fixtures\FirstAlternatingRelatedModel;
use Hofff\JsonLd\Test\Fixtures\SecondAlternatingRelatedModel;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class CircularAlternatingRelatedModelTest extends AbstractFunctionalTest
{
    /**
     * @var FirstAlternatingRelatedModel
     */
    protected $first;

    /**
     * @var SecondAlternatingRelatedModel
     */
    protected $second;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->first = new FirstAlternatingRelatedModel();
        $this->second = new SecondAlternatingRelatedModel();

        $this->first->second = $this->second;
        $this->second->first = $this->first;

        $this->catalog->add('https://example.com/first-alternating-related-model/circular', $this->first);
        $this->catalog->add('https://example.com/second-alternating-related-model/circular', $this->second);
    }

    public function testCircularFirst(): void
    {
        $objects = $this->normalizer->normalize($this->first);

        $normalized = [
            [
                '@id' => 'https://example.com/first-alternating-related-model/circular',
                'https://example.com/first-alternating-related-model#second' => [
                    [
                        '@id' => 'https://example.com/second-alternating-related-model/circular',
                        'https://example.com/second-alternating-related-model#first' => [
                            ['@id' => 'https://example.com/first-alternating-related-model/circular'],
                        ],
                    ],
                ],
            ],
        ];

        self::assertSame($normalized, $objects);
    }

    public function testCircularSecond(): void
    {
        $objects = $this->normalizer->normalize($this->second);

        $normalized = [
            [
                '@id' => 'https://example.com/second-alternating-related-model/circular',
                'https://example.com/second-alternating-related-model#first' => [
                    [
                        '@id' => 'https://example.com/first-alternating-related-model/circular',
                        'https://example.com/first-alternating-related-model#second' => [
                            ['@id' => 'https://example.com/second-alternating-related-model/circular'],
                        ],
                    ],
                ],
            ],
        ];

        self::assertSame($normalized, $objects);
    }
}
