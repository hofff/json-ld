<?php

declare(strict_types=1);

namespace Hofff\JsonLd\Metadata\Provider;

use Hofff\JsonLd\Metadata\Metadata;
use Psr\Cache\CacheItemPoolInterface;
use Hofff\JsonLd\Exception\MetadataUnavailable;

/**
 * Caches metadata using a PSR-6 implementation.
 *
 * @author Oliver Hoff <oliver@hofff.com>
 */
class CachingMetadataProviderDecorator implements MetadataProvider
{
    use ClassNameResolverTrait;

    /**
     * @var MetadataProvider
     */
    private $decorated;

    /**
     * @var CacheItemPoolInterface
     */
    private $cacheItemPool;

    /**
     * @param MetadataProvider $decorated
     * @param CacheItemPoolInterface $cacheItemPool
     */
    public function __construct(MetadataProvider $decorated, CacheItemPoolInterface $cacheItemPool)
    {
        $this->decorated = $decorated;
        $this->cacheItemPool = $cacheItemPool;
    }

    /**
     * {@inheritDoc}
     * @see \Hofff\JsonLd\Metadata\Provider\MetadataProvider::getMetadataFor()
     */
    public function getMetadataFor($value): Metadata
    {
        $item = $this->cacheItemPool->getItem($this->generateCacheKey($value));
        $metadata = $item->get();

        if($metadata === null || $metadata === true) {
            try {
                $metadata = $this->decorated->getMetadataFor($value);
            } catch(MetadataUnavailable $e) {
                $metadata = false;
            }

            $this->cacheItemPool->save($item->set($metadata));
        }

        if ($metadata === false) {
            throw new MetadataUnavailable();
        }

        return $metadata;
    }

    /**
     * {@inheritDoc}
     * @see \Hofff\JsonLd\Metadata\Provider\MetadataProvider::isMetadataAvailableFor()
     */
    public function isMetadataAvailableFor($value): bool
    {
        $item = $this->cacheItemPool->getItem($this->generateCacheKey($value));

        if($item->isHit()) {
            return $item->get() !== false;
        }

        $available = $this->decorated->isMetadataAvailableFor($value);
        $this->cacheItemPool->save($item->set($available));

        return $available;
    }

    /**
     * @param object $value
     * @return string
     */
    private function generateCacheKey($value): string
    {
        $class = $this->resolveClassName($value);
        // key cannot contain backslashes according to PSR-6
        $key = strtr($class, '\\', '_');

        return $key;
    }
}
