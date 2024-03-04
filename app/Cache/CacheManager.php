<?php

namespace app\Cache;

use Exception;
use Illuminate\Support\Facades\Cache;

class CacheManager
{
    final public const SERVER_LIST_KEY    = 'SERVER_LIST';
    final public const LOCATION_LIST_KEY = 'LOCATION_LIST';

    private array $cacheType = [
        self::SERVER_LIST_KEY   => ['ttl' => 3000],
        self::LOCATION_LIST_KEY => ['ttl' => 3000],
    ];

    public function __construct()
    {
    }

    public function get(string $key, string $cacheKeyType = ''): array
    {

        $key = $cacheKeyType . '_' . $key;

        if (Cache::has($key)) {
            $cachedValue = Cache::get($key);

            if ($cachedValue) {
                return $cachedValue;
            }
        }

        return [];
    }

    public function set(string $key, string $cacheKeyType, $value = null): void
    {

        $key = $cacheKeyType . '_' . $key;
        $ttl = $this->getTtl($cacheKeyType);

        Cache::put($key, $value, $ttl);
    }

    private function getTtl($cacheKeyType): int
    {
        if (array_key_exists($cacheKeyType, $this->cacheType)) {
            return $this->cacheType[$cacheKeyType]['ttl'];
        }

        return 0;
    }
}
