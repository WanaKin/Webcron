<?php

namespace Tests;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class WorkerHttpTest extends TestCase
{
    public function test_calls_worker()
    {
        $mock = \Mockery::mock();

        $mock->shouldReceive('call')
            ->once();

        // The artisan facade doesn't support mocking as-is
        Artisan::swap($mock);

        Cache::shouldReceive('get')
            ->once()
            ->with('webcron_worker_lock')
            ->andReturnNull();

        Cache::shouldReceive('put')
            ->once()
            ->with('webcron_worker_lock', any(Carbon::class));

        Cache::shouldReceive('forget')
            ->once()
            ->with('webcron_worker_lock');

        $this->get(route('worker'))
            ->assertNoContent();
    }

    public function test_does_not_call_worker_when_locked()
    {
        Cache::shouldReceive('get')
            ->once()
            ->with('webcron_worker_lock')
            ->andReturn(now());

        $mock = \Mockery::mock();

        $mock->shouldNotReceive('call');

        Artisan::swap($mock);

        $this->get(route('worker'))
            ->assertNoContent();
    }
}