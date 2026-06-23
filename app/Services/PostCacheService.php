<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class PostCacheService
{
    private const VERSION_KEY = 'posts.cache_version';

    public function remember(string $suffix, callable $callback, int $ttl = 3600): mixed
    {
        $version = Cache::get(self::VERSION_KEY, 1);

        return Cache::remember("posts.{$version}.{$suffix}", $ttl, $callback);
    }

    public function clear(): void
    {
        if (Cache::has(self::VERSION_KEY)) {
            Cache::increment(self::VERSION_KEY);
        } else {
            Cache::put(self::VERSION_KEY, 2);
        }
    }
}
