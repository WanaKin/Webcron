<?php

namespace Tests\Feature;

use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class WorkerHttpTest extends TestCase
{
    public function test_calls_worker()
    {
        Artisan::swap(\Mockery::mock()->shouldReceive('call')->getMock());

        $lock = $this->mock(Lock::class);

        $lock->shouldReceive('get')
            ->once()
            ->andReturnSelf();

        $lock->shouldReceive('release')
            ->once();

        Cache::shouldReceive('lock')
            ->once()
            ->andReturn($lock);

        $this->get(route('worker'))
            ->assertNoContent();
    }

    public function test_does_not_call_worker_when_locked()
    {
        Artisan::swap(\Mockery::mock()->shouldNotReceive('call')->getMock());

        $lock = $this->mock(Lock::class)
            ->shouldReceive('get')
            ->once()
            ->andReturnFalse()
            ->getMock();

        Cache::shouldReceive('lock')
            ->once()
            ->andReturn($lock);

        $this->get(route('worker'))
            ->assertNoContent();
    }

    public function test_nothing_happens_when_disabled()
    {
        $this->app['config']->set('webcron.worker.enabled', false);

        $this->mock(Cache::getStore()::class)
            ->shouldNotReceive('lock');

        Artisan::swap(\Mockery::mock()->shouldNotReceive('call')->getMock());

        $this->get(route('worker'))
            ->assertNoContent();
    }
}