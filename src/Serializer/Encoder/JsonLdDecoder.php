<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Serializer\Encoder;

use Hofff\JsonLd\Serializer\Constants;
use Symfony\Component\Serializer\Encoder\ContextAwareDecoderInterface;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class JsonLdDecoder implements ContextAwareDecoderInterface
{
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Serializer\Encoder\ContextAwareDecoderInterface::supportsDecoding()
     */
    public function supportsDecoding($format, array $context = array()): bool
    {
        return $format === Constants::FORMAT || $format === Constants::MIME_TYPE;
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Serializer\Encoder\DecoderInterface::decode()
     */
    public function decode($data, $format, array $context = [])
    {
        // TODO

        return nulL;
    }
}
