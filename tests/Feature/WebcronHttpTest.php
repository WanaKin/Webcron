<?php

namespace Tests;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use WanaKin\Webcron\Facades\WebcronScheduler;
use function any;
use function anyArgs;

class WebcronHttpTest extends TestCase
{
    public function test_runs_first_time()
    {
        WebcronScheduler::shouldReceive('dispatchScheduledJobs')
            ->once();

        Cache::shouldReceive('get')
            ->once()
            ->with('webcron_scheduler_lock')
            ->andReturnNull();

        Cache::shouldReceive('put')
            ->once()
            ->with('webcron_scheduler_lock', any(Carbon::class));

        Cache::shouldReceive('forget')
            ->once()
            ->with('webcron_scheduler_lock');

        $this->get(route('cron'))
            ->assertNoContent();
    }

    public function test_does_not_run_twice_in_one_minute()
    {
        Cache::shouldReceive('get')
            ->once()
            ->with('webcron_scheduler_lock')
            ->andReturn(now()->subSeconds(15));

        $this->mock(WebcronScheduler::getFacadeAccessor())
            ->shouldNotReceive('dispatchScheduledJobs');

        $this->get(route('cron'))
            ->assertNoContent();
    }
}