<?php

namespace Tests\Feature;

use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use WanaKin\Webcron\Facades\WebcronScheduler;

class WebcronHttpTest extends TestCase
{
    public function test_runs_first_time()
    {
        WebcronScheduler::shouldReceive('dispatchScheduledJobs')
            ->once();

        $lock = \Mockery::mock(Lock::class);

        $lock->shouldReceive('get')
            ->once()
            ->andReturnSelf();

        $lock->shouldReceive('release')
            ->once();

        Cache::shouldReceive('lock')
            ->andReturn($lock);

        $this->get(route('cron'))
            ->assertNoContent();
    }

    public function test_does_not_run_twice_in_one_minute()
    {
        $lock = $this->mock(Lock::class)
            ->shouldReceive('get')
            ->once()
            ->andReturnFalse()
            ->getMock();

        Cache::shouldReceive('lock')
            ->andReturn($lock);

        $this->mock(WebcronScheduler::getFacadeAccessor())
            ->shouldNotReceive('dispatchScheduledJobs');

        $this->get(route('cron'))
            ->assertNoContent();
    }

    public function test_nothing_happens_when_disabled()
    {
        $this->app['config']->set('webcron.scheduler.enabled', false);

        $this->mock(WebcronScheduler::getFacadeAccessor())
            ->shouldNotReceive('dispatchScheduledJobs');

        $this->get(route('cron'))
            ->assertNoContent();
    }
}