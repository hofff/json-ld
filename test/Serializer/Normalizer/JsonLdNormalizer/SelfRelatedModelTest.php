<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Test\Serializer\Normalizer\JsonLdNormalizer;

use Hofff\JsonLd\Test\Fixtures\SelfRelatedModel;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class SelfRelatedModelTest extends AbstractFunctionalTest
{
    /**
     * @var SelfRelatedModel
     */
    protected $model;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new SelfRelatedModel();

        $this->model->self = $this->model;

        $this->catalog->add('https://example.com/self-related-model/0', $this->model);
    }

    public function testNormalization(): void
    {
        $objects = $this->normalizer->normalize($this->model);

        $normalized = [
            [
                '@id' => 'https://example.com/self-related-model/0',
                'https://example.com/self-related-model#self' => [
                    ['@id' => 'https://example.com/self-related-model/0'],
                ],
            ],
        ];

        self::assertSame($normalized, $objects);
    }
}
