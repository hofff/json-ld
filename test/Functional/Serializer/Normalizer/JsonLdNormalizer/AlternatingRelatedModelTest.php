<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Test\Functional\Serializer\Normalizer\JsonLdNormalizer;

use Hofff\JsonLd\Test\Fixtures\FirstAlternatingRelatedModel;
use Hofff\JsonLd\Test\Fixtures\SecondAlternatingRelatedModel;
use Hofff\JsonLd\Serializer\Normalizer\JsonLdNormalizer;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class AlternatingRelatedModelTest extends AbstractFunctionalTest
{
    /**
     * @var FirstAlternatingRelatedModel
     */
    protected $firstZero;

    /**
     * @var SecondAlternatingRelatedModel
     */
    protected $secondZero;

    /**
     * @var FirstAlternatingRelatedModel
     */
    protected $firstOne;

    /**
     * @var SecondAlternatingRelatedModel
     */
    protected $secondOne;

    /**
     * @var FirstAlternatingRelatedModel
     */
    protected $firstTwo;

    /**
     * @var SecondAlternatingRelatedModel
     */
    protected $secondTwo;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->firstZero = new FirstAlternatingRelatedModel();
        $this->secondZero = new SecondAlternatingRelatedModel();
        $this->firstOne = new FirstAlternatingRelatedModel();
        $this->secondOne = new SecondAlternatingRelatedModel();
        $this->firstTwo = new FirstAlternatingRelatedModel();
        $this->secondTwo = new SecondAlternatingRelatedModel();

        $this->firstZero->second = $this->secondZero;
        $this->secondZero->first = $this->firstOne;
        $this->firstOne->second = $this->secondOne;
        $this->secondOne->first = $this->firstTwo;
        $this->firstTwo->second = $this->secondTwo;

        $this->catalog->add('https://example.com/first-alternating-related-model/0', $this->firstZero);
        $this->catalog->add('https://example.com/second-alternating-related-model/0', $this->secondZero);
        $this->catalog->add('https://example.com/first-alternating-related-model/1', $this->firstOne);
        $this->catalog->add('https://example.com/second-alternating-related-model/1', $this->secondOne);
        $this->catalog->add('https://example.com/first-alternating-related-model/2', $this->firstTwo);
        $this->catalog->add('https://example.com/second-alternating-related-model/2', $this->secondTwo);
    }

    public function testFirstZero(): void
    {
        $context = [];
        $context[JsonLdNormalizer::KEY_ENABLE_MAX_DEPTH] = true;

        $objects = $this->normalizer->normalize($this->firstZero, null, $context);

        $normalized = [
            [
                '@id' => 'https://example.com/first-alternating-related-model/0',
                'https://example.com/first-alternating-related-model#second' => [
                    [
                        '@id' => 'https://example.com/second-alternating-related-model/0',
                        'https://example.com/second-alternating-related-model#first' => [
                            [
                                '@id' => 'https://example.com/first-alternating-related-model/1',
                                'https://example.com/first-alternating-related-model#second' => [
                                    ['@id' => 'https://example.com/second-alternating-related-model/1'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        self::assertSame($normalized, $objects);
    }

    public function testSecondZero(): void
    {
        $context = [];
        $context[JsonLdNormalizer::KEY_ENABLE_MAX_DEPTH] = true;

        $objects = $this->normalizer->normalize($this->secondZero, null, $context);

        $normalized = [
            [
                '@id' => 'https://example.com/second-alternating-related-model/0',
                'https://example.com/second-alternating-related-model#first' => [
                    [
                        '@id' => 'https://example.com/first-alternating-related-model/1',
                        'https://example.com/first-alternating-related-model#second' => [
                            [
                                '@id' => 'https://example.com/second-alternating-related-model/1',
                                'https://example.com/second-alternating-related-model#first' => [
                                    [
                                        '@id' => 'https://example.com/first-alternating-related-model/2',
                                        'https://example.com/first-alternating-related-model#second' => [
                                            ['@id' => 'https://example.com/second-alternating-related-model/2'],
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
