<?php

namespace WanaKin\Webcron\Http\Controllers;

use Illuminate\Contracts\Cache\Lock;
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
     * Lock a process if possible
     *
     * @param string $key
     * @return Lock|false
     */
    protected function lock(string $key): Lock|bool
    {
        // Always fail if disabled
        if (!config("webcron.{$key}.enabled")) {
            return false;
        }

        $timeout = config("webcron.{$key}.lock_timeout") ?? config('webcron.lock_timeout');

        $lock = Cache::lock($this->cacheKey($key), $timeout);

        return $lock->get() ? $lock : false;
    }
}