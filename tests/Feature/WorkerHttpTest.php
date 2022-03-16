<?php

namespace Tests\Feature;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class WorkerHttpTest extends TestCase
{
    public function test_calls_worker()
    {
        Artisan::swap(\Mockery::mock()->shouldReceive('call')->getMock());

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

        Artisan::swap(\Mockery::mock()->shouldNotReceive('call')->getMock());

        $this->get(route('worker'))
            ->assertNoContent();
    }

    public function test_nothing_happens_when_disabled()
    {
        $this->app['config']->set('webcron.worker.enabled', false);

        $this->mock(Cache::getStore()::class)
            ->shouldNotReceive('get');

        Artisan::swap(\Mockery::mock()->shouldNotReceive('call')->getMock());

        $this->get(route('worker'))
            ->assertNoContent();
    }
}