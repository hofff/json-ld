<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Serializer\Normalizer;

use Hofff\JsonLd\Exception\NotSupported;
use Hofff\JsonLd\Serializer\Constants;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class JsonLdDenormalizer extends AbstractNormalizer implements DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Serializer\Normalizer\DenormalizerInterface::supportsDenormalization()
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = [])
    {
        return $format === Constants::FORMAT || $format === Constants::MIME_TYPE;
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Serializer\Normalizer\DenormalizerInterface::denormalize()
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        // TODO

        return null;
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Serializer\Normalizer\NormalizerInterface::supportsNormalization()
     */
    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return false;
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Serializer\Normalizer\NormalizerInterface::normalize()
     */
    public function normalize($things, $format = null, array $context = [])
    {
        throw new NotSupported();
    }
}
