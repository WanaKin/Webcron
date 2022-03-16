<?php

namespace WanaKin\Webcron\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class Controller extends \Illuminate\Routing\Controller
{
    /**
     * Generate a full cache key
     *
     * @param  string $key
     * @return string
     */
    protected function cacheKey(string $key): string
    {
        return "webcron_{$key}_lock";
    }

    /**
     * Check if a process is locked
     *
     * @param  string $key
     * @return bool
     */
    protected function locked(string $key): bool
    {
        // Always return true if disabled
        if (!config("webcron.{$key}.enabled")) {
            return true;
        }

        /** @var ?Carbon */
        $lock = Cache::get($this->cacheKey($key));

        $timeout = config("webcron.{$key}.lock_timeout") ?? config('webcron.lock_timeout');

        if (!$lock || $lock->add($timeout)->lt(now())) {
            return false;
        }

        return true;
    }

    /**
     * Lock a resource
     *
     * @param string $key
     * @return void
     */
    protected function lock(string $key): void
    {
        Cache::put($this->cacheKey($key), now());
    }

    /**
     * Unlock a resource
     *
     * @param  string $key
     * @return void
     */
    protected function unlock(string $key): void
    {
        Cache::forget($this->cacheKey($key));
    }
}