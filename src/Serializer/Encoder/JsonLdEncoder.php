<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Serializer\Encoder;

use Hofff\JsonLd\Serializer\Constants;
use ML\JsonLD\JsonLD;
use Symfony\Component\Serializer\Encoder\ContextAwareEncoderInterface;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class JsonLdEncoder implements ContextAwareEncoderInterface
{
    const KEY_DOCUMENT_FORM = 'hofff_jsonld_document_form';
    const KEY_CONTEXT = 'hofff_jsonld_context';

    const DOCUMENT_FORM_EXPANDED = 'expanded';
    const DOCUMENT_FORM_COMPACTED = 'compacted';
    const DOCUMENT_FORM_FLATTENED = 'flattened';

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Serializer\Encoder\ContextAwareEncoderInterface::supportsEncoding()
     */
    public function supportsEncoding($format, array $context = []): bool
    {
        return $format === Constants::FORMAT || $format === Constants::MIME_TYPE;
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Serializer\Encoder\EncoderInterface::encode()
     */
    public function encode($data, $format, array $context = [])
    {
        switch($context[self::KEY_DOCUMENT_FORM] ?? self::DOCUMENT_FORM_EXPANDED) {
            default:
            case self::DOCUMENT_FORM_EXPANDED:
                $data = JsonLD::expand($data);
                break;

            case self::DOCUMENT_FORM_COMPACTED:
                $data = JsonLD::compact($data, $context[self::KEY_CONTEXT] ?? null);
                break;

            case self::DOCUMENT_FORM_FLATTENED:
                $data = JsonLD::flatten($data);
                break;
        }

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
